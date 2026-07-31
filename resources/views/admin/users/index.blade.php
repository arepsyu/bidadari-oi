@extends('layouts.app')
@section('title', 'Kelola Akun User')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <form method="GET" class="d-flex gap-2 flex-wrap">
        <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control form-control-sm" placeholder="Cari nama / username / organisasi..." style="min-width: 260px;">
        <select name="kategori" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">-- Semua Kategori --</option>
            <option value="admin" {{ ($kategori ?? '') === 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="opd" {{ ($kategori ?? '') === 'opd' ? 'selected' : '' }}>OPD/Dinas</option>
            <option value="kecamatan" {{ ($kategori ?? '') === 'kecamatan' ? 'selected' : '' }}>Kecamatan</option>
            <option value="desa" {{ ($kategori ?? '') === 'desa' ? 'selected' : '' }}>Desa</option>
        </select>
        <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-search"></i> Cari</button>
        @if(($search ?? '') !== '' || ($kategori ?? '') !== '')
            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
        @endif
    </form>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Tambah Akun
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Nama</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Kategori</th>
                    <th>Organisasi</th>
                    <th>Status</th>
                    <th class="text-end pe-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td class="ps-3">{{ $user->name }}</td>
                    <td>{{ $user->username }}</td>
                    <td>
                        <span class="badge {{ $user->role === 'admin' ? 'bg-primary' : 'bg-secondary' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td>{{ $user->role === 'user' ? $user->kategoriLabel() : '-' }}</td>
                    <td>{{ $user->organisasi ?? '-' }}</td>
                    <td>
                        @if($user->is_active)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-danger">Nonaktif</span>
                        @endif
                    </td>
                    <td class="text-end pe-3">
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('{{ $user->submissions_count > 0 ? '⚠️ PERINGATAN: Akun ini punya ' . $user->submissions_count . ' data yang udah diupload. Semua data itu bakal IKUT TERHAPUS PERMANEN kalau lanjut. Yakin mau hapus akun ini?' : 'Yakin hapus akun ini?' }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada akun.</td></tr>
                @endforelse
            </tbody>
        </table>
            </div>
    </div>
</div>

<div class="mt-3">
    {{ $users->links() }}
</div>
@endsection
