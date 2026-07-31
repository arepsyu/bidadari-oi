<?php

namespace App\Http\Controllers\Admin;

use App\Exports\MonitoringExport;
use App\Exports\UserDetailExport;
use App\Http\Controllers\Controller;
use App\Models\Pertanyaan;
use App\Models\Submission;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;

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

        return view('admin.monitoring.index', compact(
            'totalUsers',
            'totalPertanyaan',
            'totalSubmissions',
            'completionRate',
            'users',
            'belumLengkap',
            'sudahLengkap',
            'perKategori',
            'menungguVerifikasi'
        ));
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

    /**
     * Download semua dokumen yang diupload 1 akun sekaligus, dikompres jadi ZIP.
     * Kalau ada data per-desa (khusus akun Kecamatan), dikelompokin ke subfolder
     * per desa biar gak campur aduk.
     */
    public function downloadZip(User $user): Response|RedirectResponse
    {
        $submissions = Submission::where('user_id', $user->id)
            ->whereNotNull('file_path')
            ->with(['pertanyaan', 'desa'])
            ->orderBy('pertanyaan_id')
            ->get();

        if ($submissions->isEmpty()) {
            return back()->with('error', 'Belum ada dokumen yang diupload akun ini.');
        }

        $zipFileName = 'dokumen-' . Str::slug($user->organisasi ?? $user->name) . '-' . now()->format('Ymd_His') . '.zip';
        $zipDir = storage_path('app/temp-zip');

        if (! is_dir($zipDir)) {
            mkdir($zipDir, 0755, true);
        }

        $zipPath = $zipDir . '/' . $zipFileName;

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Gagal membuat file ZIP.');
        }

        $counter = 1;
        foreach ($submissions as $sub) {
            $fullPath = public_path($sub->file_path);

            if (! file_exists($fullPath) || ! $sub->pertanyaan) {
                continue;
            }

            $ext = pathinfo($sub->file_original_name ?? $sub->file_path, PATHINFO_EXTENSION) ?: 'dat';
            $judul = Str::limit(Str::slug($sub->pertanyaan->teks, '-'), 60, '');
            $namaFile = sprintf('%02d_%s.%s', $counter, $judul ?: 'dokumen', $ext);

            if ($sub->desa_id && $sub->desa) {
                $folder = 'Desa - ' . Str::slug($sub->desa->nama, '-');
                $zip->addFile($fullPath, $folder . '/' . $namaFile);
            } else {
                $zip->addFile($fullPath, $namaFile);
            }

            $counter++;
        }

        $zip->close();

        if ($counter === 1) {
            @unlink($zipPath);
            return back()->with('error', 'Gagal menemukan file dokumen di server (mungkin sudah terhapus).');
        }

        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }
}
