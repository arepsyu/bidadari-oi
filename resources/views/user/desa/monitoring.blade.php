@extends('layouts.app')
@section('title', 'Monitoring Desa')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card stat-card blue p-3">
            <div class="small opacity-75">Total Desa/Kelurahan</div>
            <div class="fs-3 fw-bold">{{ $desas->count() }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card green p-3">
            <div class="small opacity-75">Desa Sudah Lengkap (100%)</div>
            <div class="fs-3 fw-bold">{{ $desaLengkap }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card green p-3">
            <div class="small opacity-75">Rata-rata Kelengkapan</div>
            <div class="fs-3 fw-bold">{{ $rataProgress }}%</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white fw-semibold">Kelengkapan Data per Desa/Kelurahan</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Desa/Kelurahan</th>
                        <th>Data Terisi</th>
                        <th style="width: 220px;">Progres</th>
                        <th class="text-end pe-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($desas as $desa)
                    <tr>
                        <td class="ps-3">{{ $desa->nama }}</td>
                        <td>{{ $desa->terisi_count }} / {{ $desa->total_pertanyaan }}</td>
                        <td>
                            <div class="progress">
                                <div class="progress-bar" style="width: {{ $desa->progress }}%"></div>
                            </div>
                            <div class="small text-muted mt-1">{{ $desa->progress }}%</div>
                        </td>
                        <td class="text-end pe-3">
                            <a href="{{ route('user.desa.show', $desa) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil-square"></i> Isi Data
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-4 text-muted">Belum ada data desa di wilayah kecamatan ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
