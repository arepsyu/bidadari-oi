<?php

namespace App\Http\Controllers\Admin;

use App\Exports\MonitoringExport;
use App\Exports\UserDetailExport;
use App\Http\Controllers\Controller;
use App\Models\Klaster;
use App\Models\Pertanyaan;
use App\Models\Submission;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class MonitoringController extends Controller
{
    public function index(): View
    {
        $totalUsers = User::where('role', 'user')->count();
        $totalPertanyaan = Pertanyaan::count();
        $totalSubmissions = Submission::count();
        $menungguVerifikasi = Submission::where('status', 'menunggu')->count();

        $users = User::where('role', 'user')
            ->with('opd')
            ->withCount('submissions')
            ->withCount(['submissions as menunggu_count' => function ($q) {
                $q->where('status', 'menunggu');
            }])
            ->orderBy('name')
            ->get()
            ->map(function ($user) {
                $relevanCount = $user->pertanyaanRelevan()->count();
                $user->relevan_count = $relevanCount;
                $user->progress = $relevanCount > 0
                    ? round(($user->submissions_count / $relevanCount) * 100)
                    : 0;
                return $user;
            });

        $targetTotal = $users->sum('relevan_count');
        $isiTotal = $users->sum('submissions_count');
        $completionRate = $targetTotal > 0 ? round(($isiTotal / $targetTotal) * 100) : 0;

        $belumLengkap = $users->where('progress', '<', 100)->count();
        $sudahLengkap = $users->where('progress', '>=', 100)->count();

        // Kelengkapan per kategori (OPD / Kecamatan / Desa)
        $perKategori = $users->groupBy('kategori')->map(function ($grup) {
            return [
                'jumlah_akun' => $grup->count(),
                'rata_progress' => $grup->count() > 0 ? round($grup->avg('progress')) : 0,
            ];
        });

        [$klasterSkor, $totalEstimasi, $totalMaxSkor, $persenSkor] = $this->hitungEstimasiSkor();

        return view('admin.monitoring.index', compact(
            'totalUsers',
            'totalPertanyaan',
            'totalSubmissions',
            'completionRate',
            'users',
            'belumLengkap',
            'sudahLengkap',
            'perKategori',
            'menungguVerifikasi',
            'klasterSkor',
            'totalEstimasi',
            'totalMaxSkor',
            'persenSkor'
        ));
    }

    /**
     * Hitung estimasi skor KLA: tiap pertanyaan yang punya minimal 1 data
     * berstatus "disetujui" dianggap tercapai, nilai_max-nya masuk ke total.
     * Dihitung per-pertanyaan (bukan per-submission) biar pertanyaan yang
     * dijawab banyak akun sekaligus (kecamatan/desa) gak keitung dobel.
     */
    private function hitungEstimasiSkor(): array
    {
        $pertanyaanDisetujui = Submission::where('status', 'disetujui')
            ->distinct()
            ->pluck('pertanyaan_id');

        $klasters = Klaster::with('indikators.pertanyaans')->orderBy('urutan')->get();

        $klasterSkor = $klasters->map(function ($klaster) use ($pertanyaanDisetujui) {
            $max = 0;
            $estimasi = 0;

            foreach ($klaster->indikators as $indikator) {
                foreach ($indikator->pertanyaans as $p) {
                    $nilaiMax = (float) ($p->nilai_max ?? 0);
                    $max += $nilaiMax;
                    if ($pertanyaanDisetujui->contains($p->id)) {
                        $estimasi += $nilaiMax;
                    }
                }
            }

            return [
                'nama' => $klaster->nama,
                'estimasi' => $estimasi,
                'max' => $max,
                'persen' => $max > 0 ? round(($estimasi / $max) * 100) : 0,
            ];
        });

        $totalEstimasi = round($klasterSkor->sum('estimasi'), 2);
        $totalMaxSkor = round($klasterSkor->sum('max'), 2);
        $persenSkor = $totalMaxSkor > 0 ? round(($totalEstimasi / $totalMaxSkor) * 100) : 0;

        return [$klasterSkor, $totalEstimasi, $totalMaxSkor, $persenSkor];
    }

    public function show(User $user): View
    {
        $pertanyaans = $user->pertanyaanRelevan()->with('indikator.klaster')->get()
            ->groupBy(fn ($p) => $p->indikator->klaster->nama . '|' . $p->indikator->nama);

        $submissions = Submission::where('user_id', $user->id)->get()->keyBy('pertanyaan_id');

        return view('admin.monitoring.show', compact('user', 'pertanyaans', 'submissions'));
    }

    public function exportExcel(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $filename = 'monitoring-bidadari-oi-' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new MonitoringExport, $filename);
    }

    public function exportPdf(): Response
    {
        $users = User::where('role', 'user')
            ->withCount('submissions')
            ->withCount(['submissions as menunggu_count' => function ($q) {
                $q->where('status', 'menunggu');
            }])
            ->orderBy('name')
            ->get()
            ->map(function ($user) {
                $relevanCount = $user->pertanyaanRelevan()->count();
                $user->relevan_count = $relevanCount;
                $user->progress = $relevanCount > 0
                    ? round(($user->submissions_count / $relevanCount) * 100)
                    : 0;
                return $user;
            });

        $pdf = Pdf::loadView('admin.monitoring.pdf', compact('users'))->setPaper('a4', 'landscape');

        return $pdf->download('monitoring-bidadari-oi-' . now()->format('Ymd_His') . '.pdf');
    }

    public function exportUserExcel(User $user): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $filename = 'data-' . str($user->organisasi ?? $user->name)->slug() . '.xlsx';
        return Excel::download(new UserDetailExport($user), $filename);
    }

    public function exportUserPdf(User $user): Response
    {
        $pertanyaans = $user->pertanyaanRelevan()->with('indikator.klaster')->get()
            ->groupBy(fn ($p) => $p->indikator->klaster->nama . '|' . $p->indikator->nama);

        $submissions = Submission::where('user_id', $user->id)->get()->keyBy('pertanyaan_id');

        $pdf = Pdf::loadView('admin.monitoring.pdf_detail', compact('user', 'pertanyaans', 'submissions'))
            ->setPaper('a4', 'portrait');

        $filename = 'data-' . str($user->organisasi ?? $user->name)->slug() . '.pdf';
        return $pdf->download($filename);
    }
}
