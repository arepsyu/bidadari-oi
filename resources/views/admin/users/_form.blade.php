<div class="mb-3">
    <label class="form-label">Nama</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name ?? '') }}" required>
    @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
</div>
<div class="mb-3">
    <label class="form-label">Username</label>
    <input type="text" name="username" class="form-control" value="{{ old('username', $user->username ?? '') }}" placeholder="Contoh: kecamatan.indralaya" required>
    <div class="form-text">Cukup pakai huruf, angka, titik, atau strip — gak perlu format email.</div>
    @error('username') <div class="text-danger small">{{ $message }}</div> @enderror
</div>
<div class="mb-3">
    <label class="form-label">Password {{ isset($user) ? '(kosongkan jika tidak diubah)' : '' }}</label>
    <input type="password" name="password" class="form-control" {{ isset($user) ? '' : 'required' }}>
    @error('password') <div class="text-danger small">{{ $message }}</div> @enderror
</div>
<div class="mb-3">
    <label class="form-label">Role</label>
    <select name="role" id="role" class="form-select" required onchange="toggleKategoriBlock()">
        <option value="user" {{ old('role', $user->role ?? 'user') === 'user' ? 'selected' : '' }}>User</option>
        <option value="admin" {{ old('role', $user->role ?? '') === 'admin' ? 'selected' : '' }}>Admin</option>
    </select>
    <div class="form-text">Admin punya akses penuh kelola sistem. User cuma bisa isi data sesuai kategori akunnya.</div>
</div>

<div id="kategori-block">
    <div class="mb-3">
        <label class="form-label">Kategori Akun</label>
        <select name="kategori" id="kategori" class="form-select" onchange="toggleOpdBlock()">
            <option value="">-- Pilih Kategori --</option>
            <option value="opd" {{ old('kategori', $user->kategori ?? '') === 'opd' ? 'selected' : '' }}>OPD / Dinas</option>
            <option value="kecamatan" {{ old('kategori', $user->kategori ?? '') === 'kecamatan' ? 'selected' : '' }}>Kecamatan</option>
            <option value="desa" {{ old('kategori', $user->kategori ?? '') === 'desa' ? 'selected' : '' }}>Desa</option>
        </select>
        @error('kategori') <div class="text-danger small">{{ $message }}</div> @enderror
        <div class="form-text">
            Menentukan pertanyaan mana yang bakal muncul buat akun ini. Akun "Desa" otomatis lihat semua
            pertanyaan yang ditandai khusus Desa, "Kecamatan" otomatis lihat pertanyaan khusus Kecamatan,
            sedangkan "OPD/Dinas" cuma lihat pertanyaan yang di-assign ke OPD-nya masing-masing.
        </div>
    </div>

    <div class="mb-3" id="opd-block">
        <label class="form-label">Pilih OPD/Dinas</label>
        <select name="opd_id" class="form-select">
            <option value="">-- Pilih OPD --</option>
            @foreach($opds as $opd)
                <option value="{{ $opd->id }}" {{ old('opd_id', $user->opd_id ?? '') == $opd->id ? 'selected' : '' }}>
                    {{ $opd->nama }}
                </option>
            @endforeach
        </select>
        @error('opd_id') <div class="text-danger small">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3" id="organisasi-block">
        <label class="form-label">Nama Kecamatan/Desa</label>
        <input type="text" name="organisasi" class="form-control" placeholder="Contoh: Desa Sukajadi"
               value="{{ old('organisasi', $user->organisasi ?? '') }}">
        <div class="form-text">Khusus kategori Kecamatan/Desa, isi nama kecamatan/desanya di sini.</div>
    </div>
</div>

@if(isset($user))
<div class="form-check form-switch mb-3">
    <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
    <label class="form-check-label" for="is_active">Akun Aktif</label>
</div>
@endif

<script>
    function toggleKategoriBlock() {
        const role = document.getElementById('role').value;
        document.getElementById('kategori-block').style.display = role === 'admin' ? 'none' : 'block';
    }
    function toggleOpdBlock() {
        const kategori = document.getElementById('kategori').value;
        document.getElementById('opd-block').style.display = kategori === 'opd' ? 'block' : 'none';
        document.getElementById('organisasi-block').style.display = (kategori === 'kecamatan' || kategori === 'desa') ? 'block' : 'none';
    }
    toggleKategoriBlock();
    toggleOpdBlock();
</script>
