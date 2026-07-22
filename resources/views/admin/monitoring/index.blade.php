@extends('layouts.app')
@section('title', 'Dashboard Monitoring')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h6 class="text-muted mb-0">Ringkasan kelengkapan data seluruh organisasi</h6>
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
            <div class="small opacity-75">Total Akun User</div>
            <div class="fs-3 fw-bold">{{ $totalUsers }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card green p-3">
            <div class="small opacity-75">Jenis Data</div>
            <div class="fs-3 fw-bold">{{ $totalRequirements }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card blue p-3">
            <div class="small opacity-75">Total Data Terupload</div>
            <div class="fs-3 fw-bold">{{ $totalSubmissions }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card green p-3">
            <div class="small opacity-75">Kelengkapan Data Keseluruhan</div>
            <div class="fs-3 fw-bold">{{ $completionRate }}%</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold">Status Organisasi</div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="chartStatus" style="max-height: 220px;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold">Kelengkapan per Jenis Data</div>
            <div class="card-body">
                <canvas id="chartRequirement" style="max-height: 220px;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white fw-semibold">Progres Kelengkapan Data per User</div>
    <div class="card-body p-0">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Organisasi / User</th>
                    <th>Email</th>
                    <th>Data Terisi</th>
                    <th style="width: 220px;">Progres</th>
                    <th class="text-end pe-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td class="ps-3">
                        <div class="fw-semibold">{{ $user->organisasi ?? $user->name }}</div>
                        <div class="small text-muted">{{ $user->name }}</div>
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->submissions_count }} / {{ $totalRequirements }}</td>
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
                <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada akun user.</td></tr>
                @endforelse
            </tbody>
        </table>
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
        options: {
            plugins: { legend: { position: 'bottom' } }
        }
    });

    new Chart(document.getElementById('chartRequirement'), {
        type: 'bar',
        data: {
            labels: [@foreach($requirementStats as $r) '{{ addslashes($r->judul) }}', @endforeach],
            datasets: [{
                label: '% Organisasi Sudah Mengisi',
                data: [@foreach($requirementStats as $r) {{ $r->percentage }}, @endforeach],
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
