@extends('layouts.app')
@section('title', 'Monitoring Desa')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card blue p-3">
            <div class="small opacity-75">Total Desa/Kelurahan</div>
            <div class="fs-3 fw-bold">{{ $totalDesa }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card green p-3">
            <div class="small opacity-75">Desa Sudah Lengkap</div>
            <div class="fs-3 fw-bold">{{ $desaLengkap }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card green p-3">
            <div class="small opacity-75">Rata-rata Keterisian</div>
            <div class="fs-3 fw-bold">{{ $rataProgress }}%</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card blue p-3">
            <div class="small opacity-75">Pertanyaan Khusus Desa</div>
            <div class="fs-3 fw-bold">{{ $totalPertanyaanDesa }}</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white fw-semibold d-flex align-items-center justify-content-between">
        <span>Rekap per Kecamatan</span>
        <span class="small text-muted fw-normal">Diurutkan dari keterisian paling rendah</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Kecamatan</th>
                        <th>Jumlah Desa</th>
                        <th>Data Terisi</th>
                        <th style="width: 220px;">Progres</th>
                        <th class="text-end pe-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kecamatans as $kec)
                    <tr>
                        <td class="ps-3 fw-semibold">{{ $kec->nama }}</td>
                        <td>{{ $kec->desas_count }} desa/kelurahan</td>
                        <td>{{ $kec->terisi_count }} / {{ $kec->target_count }}</td>
                        <td>
                            <div class="progress">
                                <div class="progress-bar {{ $kec->progress < 50 ? 'bg-warning' : '' }}" style="width: {{ $kec->progress }}%"></div>
                            </div>
                            <div class="small text-muted mt-1">{{ $kec->progress }}%</div>
                        </td>
                        <td class="text-end pe-3">
                            <a href="{{ route('admin.monitoring-desa.show', $kec) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> Lihat Desa
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada data kecamatan/desa.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
