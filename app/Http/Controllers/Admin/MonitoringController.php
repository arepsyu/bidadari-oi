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
    public function downloadZip(User $user)
    {
        $submissions = Submission::where('user_id', $user->id)
            ->whereNotNull('file_path')
            ->with(['pertanyaan.indikator.klaster', 'desa'])
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
        $fallbackNo = 1;

        foreach ($submissions as $sub) {
            $fullPath = public_path($sub->file_path);
            $pertanyaan = $sub->pertanyaan;

            if (! file_exists($fullPath) || ! $pertanyaan) {
                continue;
            }

            $ext = pathinfo($sub->file_original_name ?? $sub->file_path, PATHINFO_EXTENSION) ?: 'dat';

            // Nama file: pakai kode resmi pertanyaan ("Pertanyaan 5") biar nyambung sama
            // dokumen KLA asli, plus judul singkat (4-5 kata pertama doang, biar gak kepanjangan)
            $kode = $pertanyaan->kode ? Str::slug($pertanyaan->kode, '-') : 'dokumen-' . $fallbackNo++;
            $judulSingkat = Str::slug(Str::words($pertanyaan->teks, 5, ''), '-');
            $namaFile = sprintf('%s_%s.%s', $kode, $judulSingkat ?: 'lampiran', $ext);

            // Folder: kelompokin per Klaster/Indikator biar konteksnya jelas dari struktur folder,
            // bukan dari nama file yang jadi kepanjangan
            $indikator = $pertanyaan->indikator;
            $klasterNama = $indikator?->klaster?->nama ?? 'Lainnya';
            $indikatorNama = $indikator?->kode ?? $indikator?->nama ?? 'Umum';

            $folderKlasterIndikator = Str::slug($klasterNama, '-') . '/' . Str::slug($indikatorNama, '-');

            if ($sub->desa_id && $sub->desa) {
                $folder = 'Desa - ' . Str::slug($sub->desa->nama, '-') . '/' . $folderKlasterIndikator;
            } else {
                $folder = $folderKlasterIndikator;
            }

            $zip->addFile($fullPath, $folder . '/' . $namaFile);
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
