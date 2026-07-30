<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Desa;
use App\Models\Kecamatan;
use App\Models\Pertanyaan;
use App\Models\Submission;
use Illuminate\View\View;

class DesaMonitoringController extends Controller
{
    /**
     * Rekap keterisian data desa, dikelompokin per Kecamatan.
     */
    public function index(): View
    {
        $totalPertanyaanDesa = Pertanyaan::where('untuk_desa', true)->count();

        $kecamatans = Kecamatan::withCount('desas')
            ->orderBy('nama')
            ->get()
            ->map(function ($kecamatan) use ($totalPertanyaanDesa) {
                $terisi = Submission::whereHas('desa', function ($q) use ($kecamatan) {
                    $q->where('kecamatan_id', $kecamatan->id);
                })->count();

                $target = $kecamatan->desas_count * $totalPertanyaanDesa;

                $kecamatan->terisi_count = $terisi;
                $kecamatan->target_count = $target;
                $kecamatan->progress = $target > 0 ? round(($terisi / $target) * 100) : 0;

                return $kecamatan;
            })
            ->sortBy('progress')
            ->values();

        $totalDesa = $kecamatans->sum('desas_count');
        $totalTerisi = $kecamatans->sum('terisi_count');
        $totalTarget = $kecamatans->sum('target_count');
        $rataProgress = $totalTarget > 0 ? round(($totalTerisi / $totalTarget) * 100) : 0;

        $desaLengkap = Desa::withCount('submissions')
            ->get()
            ->filter(fn ($desa) => $totalPertanyaanDesa > 0 && $desa->submissions_count >= $totalPertanyaanDesa)
            ->count();

        return view('admin.monitoring-desa.index', compact(
            'kecamatans',
            'totalDesa',
            'desaLengkap',
            'rataProgress',
            'totalPertanyaanDesa'
        ));
    }

    /**
     * Detail keterisian tiap desa di 1 kecamatan tertentu.
     */
    public function show(Kecamatan $kecamatan): View
    {
        $totalPertanyaanDesa = Pertanyaan::where('untuk_desa', true)->count();

        $desas = $kecamatan->desas()
            ->withCount('submissions')
            ->get()
            ->map(function ($desa) use ($totalPertanyaanDesa) {
                $desa->terisi_count = $desa->submissions_count;
                $desa->total_pertanyaan = $totalPertanyaanDesa;
                $desa->progress = $totalPertanyaanDesa > 0 ? round(($desa->submissions_count / $totalPertanyaanDesa) * 100) : 0;
                $desa->last_update = $desa->submissions()->max('updated_at');

                return $desa;
            })
            ->sortBy('progress')
            ->values();

        return view('admin.monitoring-desa.show', compact('kecamatan', 'desas', 'totalPertanyaanDesa'));
    }
}
