@extends('layouts.app')
@section('title', 'Ganti Password')

@section('content')
<div class="card" style="max-width: 500px;">
    <div class="card-body p-4">
        <h6 class="fw-bold mb-3">Ganti Password Akun</h6>

        <form method="POST" action="{{ route('profile.password.update') }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Password Saat Ini</label>
                <div class="input-group">
                    <input type="password" name="current_password" id="current_password" class="form-control" required>
                    <button type="button" class="btn btn-outline-secondary toggle-pw" data-target="current_password">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                @error('current_password') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Password Baru</label>
                <div class="input-group">
                    <input type="password" name="password" id="password" class="form-control" required minlength="6">
                    <button type="button" class="btn btn-outline-secondary toggle-pw" data-target="password">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                <div class="form-text">Minimal 6 karakter.</div>
                @error('password') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Ulangi Password Baru</label>
                <div class="input-group">
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required minlength="6">
                    <button type="button" class="btn btn-outline-secondary toggle-pw" data-target="password_confirmation">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-key"></i> Ganti Password
            </button>
        </form>
    </div>
</div>

<script>
    document.querySelectorAll('.toggle-pw').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const input = document.getElementById(btn.dataset.target);
            const icon = btn.querySelector('i');
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            icon.classList.toggle('bi-eye', !isHidden);
            icon.classList.toggle('bi-eye-slash', isHidden);
        });
    });
</script>
@endsection
