@extends('layouts.app')
@section('title', 'Dashboard Monitoring')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h6 class="text-muted mb-0">Ringkasan kelengkapan data seluruh OPD, Kecamatan & Desa</h6>
    <div>
        <a href="{{ route('admin.monitoring.export.excel') }}" class="btn btn-accent">
            <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
        </a>
        <a href="{{ route('admin.monitoring.export.pdf') }}" class="btn btn-primary">
            <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card blue p-3">
            <div class="small opacity-75">Total Akun</div>
            <div class="fs-3 fw-bold">{{ $totalUsers }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card green p-3">
            <div class="small opacity-75">Total Pertanyaan KLA</div>
            <div class="fs-3 fw-bold">{{ $totalPertanyaan }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3" style="border-radius:14px; color:#fff; background: linear-gradient(135deg, #b9770e, #f0a500);">
            <div class="small opacity-75">Menunggu Verifikasi</div>
            <div class="fs-3 fw-bold">{{ $menungguVerifikasi }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card green p-3">
            <div class="small opacity-75">Kelengkapan Keseluruhan</div>
            <div class="fs-3 fw-bold">{{ $completionRate }}%</div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-white fw-semibold d-flex align-items-center justify-content-between">
        <span><i class="bi bi-award"></i> Estimasi Skor KLA</span>
        <span class="small text-muted fw-normal">Dihitung dari data yang sudah "Disetujui" admin</span>
    </div>
    <div class="card-body">
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="p-3 rounded" style="background: var(--bo-light);">
                    <div class="small text-muted">Estimasi Skor Saat Ini</div>
                    <div class="fs-3 fw-bold text-bo-primary">{{ $totalEstimasi }} <span class="fs-6 text-muted fw-normal">/ {{ $totalMaxSkor }}</span></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 rounded" style="background: var(--bo-light);">
                    <div class="small text-muted">Persentase Pencapaian</div>
                    <div class="fs-3 fw-bold text-bo-primary">{{ $persenSkor }}%</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 rounded" style="background: var(--bo-light);">
                    <div class="small text-muted">Total Klaster Dinilai</div>
                    <div class="fs-3 fw-bold text-bo-primary">{{ $klasterSkor->count() }}</div>
                </div>
            </div>
        </div>

        <div class="small text-muted mb-2">Rincian per Klaster</div>
        @foreach($klasterSkor as $ks)
            <div class="mb-3">
                <div class="d-flex justify-content-between small mb-1">
                    <span>{{ $ks['nama'] }}</span>
                    <span class="text-muted">{{ $ks['estimasi'] }} / {{ $ks['max'] }} ({{ $ks['persen'] }}%)</span>
                </div>
                <div class="progress">
                    <div class="progress-bar {{ $ks['persen'] < 50 ? 'bg-warning' : '' }}" style="width: {{ $ks['persen'] }}%"></div>
                </div>
            </div>
        @endforeach

        <div class="small text-muted mt-2">
            <i class="bi bi-info-circle"></i> Ini estimasi internal buat pemantauan, bukan skor resmi dari KemenPPPA.
            Skor final tetap ditentukan lewat proses verifikasi lapangan oleh tim penilai KLA.
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold">Status Akun</div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="chartStatus" style="max-height: 220px;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold">Rata-rata Kelengkapan per Kategori Akun</div>
            <div class="card-body">
                <canvas id="chartKategori" style="max-height: 220px;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white fw-semibold">Progres Kelengkapan Data per Akun</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Organisasi / Akun</th>
                    <th>Kategori</th>
                    <th>Data Terisi</th>
                    <th>Menunggu Verifikasi</th>
                    <th style="width: 200px;">Progres</th>
                    <th class="text-end pe-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td class="ps-3">
                        <div class="fw-semibold">{{ $user->organisasi ?? $user->name }}</div>
                        <div class="small text-muted">{{ $user->username }}</div>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">{{ $user->kategoriLabel() }}</span>
                    </td>
                    <td>{{ $user->submissions_count }} / {{ $user->relevan_count }}</td>
                    <td>
                        @if($user->menunggu_count > 0)
                            <span class="badge bg-warning text-dark">{{ $user->menunggu_count }} pending</span>
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </td>
                    <td>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ $user->progress }}%"></div>
                        </div>
                        <div class="small text-muted mt-1">{{ $user->progress }}%</div>
                    </td>
                    <td class="text-end pe-3">
                        <a href="{{ route('admin.monitoring.show', $user) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('admin.monitoring.export.user-excel', $user) }}" class="btn btn-sm btn-outline-success">
                            <i class="bi bi-file-earmark-excel"></i>
                        </a>
                        <a href="{{ route('admin.monitoring.export.user-pdf', $user) }}" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-file-earmark-pdf"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada akun user.</td></tr>
                @endforelse
            </tbody>
        </table>
            </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    new Chart(document.getElementById('chartStatus'), {
        type: 'doughnut',
        data: {
            labels: ['Sudah Lengkap (100%)', 'Belum Lengkap'],
            datasets: [{
                data: [{{ $sudahLengkap }}, {{ $belumLengkap }}],
                backgroundColor: ['#409f74', '#e0e0e0'],
                borderWidth: 0
            }]
        },
        options: { plugins: { legend: { position: 'bottom' } } }
    });

    new Chart(document.getElementById('chartKategori'), {
        type: 'bar',
        data: {
            labels: [@foreach($perKategori as $kat => $val) '{{ ucfirst($kat) }} ({{ $val['jumlah_akun'] }} akun)', @endforeach],
            datasets: [{
                label: 'Rata-rata Kelengkapan (%)',
                data: [@foreach($perKategori as $kat => $val) {{ $val['rata_progress'] }}, @endforeach],
                backgroundColor: '#005093',
                borderRadius: 6
            }]
        },
        options: {
            scales: { y: { beginAtZero: true, max: 100 } },
            plugins: { legend: { display: false } }
        }
    });
</script>
@endsection
