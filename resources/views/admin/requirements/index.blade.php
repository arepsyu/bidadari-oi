@extends('layouts.app')
@section('title', 'Kelola Jenis Data')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="text-muted mb-0">Daftar jenis data/dokumen yang wajib diisi oleh user</h6>
    <a href="{{ route('admin.requirements.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Tambah Jenis Data
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">#</th>
                    <th>Judul</th>
                    <th>Tipe</th>
                    <th>Wajib</th>
                    <th>Jumlah Terisi</th>
                    <th class="text-end pe-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requirements as $req)
                <tr>
                    <td class="ps-3">{{ $req->urutan }}</td>
                    <td>
                        <div class="fw-semibold">{{ $req->judul }}</div>
                        <div class="small text-muted">{{ $req->deskripsi }}</div>
                    </td>
                    <td><span class="badge bg-info-subtle text-dark border">{{ strtoupper($req->tipe) }}</span></td>
                    <td>
                        @if($req->wajib)
                            <span class="badge badge-wajib">Wajib</span>
                        @else
                            <span class="badge bg-secondary">Opsional</span>
                        @endif
                    </td>
                    <td>{{ $req->submissions_count }} user</td>
                    <td class="text-end pe-3">
                        <a href="{{ route('admin.requirements.edit', $req) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('admin.requirements.destroy', $req) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus jenis data ini? Semua data user terkait akan ikut terhapus.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada jenis data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
