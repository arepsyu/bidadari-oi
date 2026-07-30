@extends('layouts.app')
@section('title', 'Monitoring Desa - ' . $kecamatan->nama)

@section('content')
<a href="{{ route('admin.monitoring-desa.index') }}" class="btn btn-sm btn-light mb-3">
    <i class="bi bi-arrow-left"></i> Kembali
</a>

<div class="card mb-3">
    <div class="card-body">
        <h6 class="fw-bold mb-1">{{ $kecamatan->nama }}</h6>
        <div class="text-muted small">{{ $desas->count() }} desa/kelurahan &middot; {{ $totalPertanyaanDesa }} pertanyaan khusus desa</div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white fw-semibold d-flex align-items-center justify-content-between">
        <span>Kelengkapan Data per Desa/Kelurahan</span>
        <span class="small text-muted fw-normal">Diurutkan dari keterisian paling rendah</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Desa/Kelurahan</th>
                        <th>Data Terisi</th>
                        <th style="width: 200px;">Progres</th>
                        <th>Terakhir Diupdate</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($desas as $desa)
                    <tr>
                        <td class="ps-3">{{ $desa->nama }}</td>
                        <td>{{ $desa->terisi_count }} / {{ $desa->total_pertanyaan }}</td>
                        <td>
                            <div class="progress">
                                <div class="progress-bar {{ $desa->progress < 50 ? 'bg-warning' : '' }}" style="width: {{ $desa->progress }}%"></div>
                            </div>
                            <div class="small text-muted mt-1">{{ $desa->progress }}%</div>
                        </td>
                        <td class="small text-muted">
                            {{ $desa->last_update ? \Carbon\Carbon::parse($desa->last_update)->format('d M Y H:i') : '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-4 text-muted">Belum ada data desa di kecamatan ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
