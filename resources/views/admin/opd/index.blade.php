@extends('layouts.app')
@section('title', 'Kelola Master OPD')

@section('content')
<div class="row g-3">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header bg-white fw-semibold">Tambah OPD Baru</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.opd.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nama OPD/Instansi</label>
                        <input type="text" name="nama" class="form-control" placeholder="Contoh: Dinas Kesehatan" required>
                        @error('nama') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Tambah</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card">
            <div class="card-header bg-white fw-semibold">Daftar Master OPD ({{ $opds->count() }})</div>
            <div class="card-body p-0">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Nama OPD</th>
                            <th>Akun</th>
                            <th>Pertanyaan</th>
                            <th class="text-end pe-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($opds as $opd)
                        <tr>
                            <td class="ps-3">
                                <form method="POST" action="{{ route('admin.opd.update', $opd) }}" class="d-flex gap-2">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" name="nama" value="{{ $opd->nama }}" class="form-control form-control-sm">
                                    <button class="btn btn-sm btn-outline-primary" type="submit"><i class="bi bi-check2"></i></button>
                                </form>
                            </td>
                            <td>{{ $opd->users_count }}</td>
                            <td>{{ $opd->pertanyaans_count }}</td>
                            <td class="text-end pe-3">
                                <form action="{{ route('admin.opd.destroy', $opd) }}" method="POST" onsubmit="return confirm('Hapus OPD ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
