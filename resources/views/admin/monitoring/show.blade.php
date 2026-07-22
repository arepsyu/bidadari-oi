@extends('layouts.app')
@section('title', 'Detail Data - ' . $user->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <a href="{{ route('admin.monitoring.index') }}" class="btn btn-sm btn-light">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
    <div>
        <a href="{{ route('admin.monitoring.export.user-excel', $user) }}" class="btn btn-sm btn-accent">
            <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
        </a>
        <a href="{{ route('admin.monitoring.export.user-pdf', $user) }}" class="btn btn-sm btn-primary">
            <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
        </a>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <h6 class="fw-bold mb-1">{{ $user->organisasi ?? '-' }}</h6>
        <div class="text-muted small">{{ $user->name }} &middot; {{ $user->email }}</div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white fw-semibold">Detail Data yang Diupload</div>
    <div class="card-body p-0">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Jenis Data</th>
                    <th>Status</th>
                    <th>Isi / File</th>
                    <th class="pe-3">Terakhir Update</th>
                </tr>
            </thead>
            <tbody>
                @foreach($requirements as $req)
                    @php $sub = $submissions->get($req->id); @endphp
                    <tr>
                        <td class="ps-3">
                            <div class="fw-semibold">{{ $req->judul }}</div>
                            @if($req->wajib)<span class="badge badge-wajib">Wajib</span>@endif
                        </td>
                        <td>
                            @if($sub)
                                <span class="badge bg-success">Sudah Diisi</span>
                            @else
                                <span class="badge bg-danger">Belum Diisi</span>
                            @endif
                        </td>
                        <td>
                            @if($sub && $req->tipe === 'file' && $sub->file_path)
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($sub->file_path) }}" target="_blank">
                                    <i class="bi bi-file-earmark-arrow-down"></i> {{ $sub->file_original_name }}
                                </a>
                            @elseif($sub)
                                {{ $sub->value }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="pe-3">{{ $sub?->updated_at?->format('d M Y H:i') ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
