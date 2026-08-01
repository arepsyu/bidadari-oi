@extends('layouts.app')
@section('title', 'Profil Saya')

@section('content')
<div class="card" style="max-width: 500px;">
    <div class="card-body p-4">
        <h6 class="fw-bold mb-3">Profil Saya</h6>

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="text-center mb-4">
                @if(auth()->user()->avatar)
                    <img src="{{ asset(auth()->user()->avatar) }}" alt="Foto Profil"
                         class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover; border: 3px solid var(--bo-light);">
                @else
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center fw-bold text-white"
                         style="width: 100px; height: 100px; background: linear-gradient(135deg, var(--bo-primary), var(--bo-secondary)); font-size: 1.75rem;">
                        {{ auth()->user()->initials() }}
                    </div>
                @endif
            </div>

            <div class="mb-3">
                <label class="form-label">Foto Profil</label>
                <input type="file" name="avatar" class="form-control" accept="image/*">
                <div class="form-text">Format JPG/PNG, maksimal 2MB.</div>
                @error('avatar') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Nama</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', auth()->user()->name) }}" required>
                @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" value="{{ auth()->user()->username }}" disabled>
                <div class="form-text">Username gak bisa diubah sendiri. Hubungi admin kalau perlu diganti.</div>
            </div>

            @if(auth()->user()->organisasi)
                <div class="mb-3">
                    <label class="form-label">Organisasi</label>
                    <input type="text" class="form-control" value="{{ auth()->user()->organisasi }}" disabled>
                </div>
            @endif

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle"></i> Simpan Perubahan
            </button>
        </form>
    </div>
</div>
@endsection
