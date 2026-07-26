@extends('layouts.app')
@section('title', 'Kelola Pertanyaan KLA')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <form method="GET" class="d-flex gap-2 flex-wrap">
        <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control form-control-sm" placeholder="Cari teks pertanyaan / indikator..." style="min-width: 260px;">
        <select name="klaster_id" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">-- Semua Klaster --</option>
            @foreach($klasters as $k)
                <option value="{{ $k->id }}" {{ (string) $klasterId === (string) $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-search"></i> Cari
        </button>
        @if(($search ?? '') !== '' || $klasterId)
            <a href="{{ route('admin.pertanyaan.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
        @endif
    </form>
    <a href="{{ route('admin.pertanyaan.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Tambah Pertanyaan
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Klaster / Indikator</th>
                    <th>Pertanyaan</th>
                    <th>Tipe</th>
                    <th>OPD/Kategori</th>
                    <th>Data Masuk</th>
                    <th class="text-end pe-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pertanyaans as $p)
                <tr>
                    <td class="ps-3" style="min-width: 160px;">
                        <div class="small text-muted">{{ $p->indikator->klaster->nama }}</div>
                        <div class="fw-semibold small">{{ $p->indikator->nama }}</div>
                    </td>
                    <td style="max-width: 320px;">
                        <div class="small">{{ \Illuminate\Support\Str::limit($p->teks, 120) }}</div>
                        @if($p->wajib)<span class="badge badge-wajib">Wajib</span>@endif
                        @if($p->wajib_lampiran)<span class="badge bg-info-subtle text-dark border">+ Lampiran</span>@endif
                    </td>
                    <td><span class="badge bg-info-subtle text-dark border">{{ strtoupper($p->tipe) }}</span></td>
                    <td style="max-width: 200px;">
                        @foreach($p->opds as $opd)
                            <span class="badge bg-light text-dark border">{{ $opd->nama }}</span>
                        @endforeach
                        @if($p->untuk_kecamatan)<span class="badge bg-warning text-dark">Semua Kecamatan</span>@endif
                        @if($p->untuk_desa)<span class="badge bg-success">Semua Desa</span>@endif
                    </td>
                    <td>{{ $p->submissions_count }}</td>
                    <td class="text-end pe-3">
                        <a href="{{ route('admin.pertanyaan.edit', $p) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('admin.pertanyaan.destroy', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus pertanyaan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada pertanyaan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $pertanyaans->links() }}</div>
@endsection
