<div class="mb-3">
    <label class="form-label">Judul Data</label>
    <input type="text" name="judul" class="form-control" placeholder="Contoh: Upload SK Organisasi" value="{{ old('judul', $requirement->judul ?? '') }}" required>
    @error('judul') <div class="text-danger small">{{ $message }}</div> @enderror
</div>
<div class="mb-3">
    <label class="form-label">Deskripsi / Petunjuk</label>
    <textarea name="deskripsi" class="form-control" rows="2">{{ old('deskripsi', $requirement->deskripsi ?? '') }}</textarea>
</div>
<div class="mb-3">
    <label class="form-label">Tipe Input</label>
    <select name="tipe" class="form-select" required>
        @php $tipeNow = old('tipe', $requirement->tipe ?? 'text'); @endphp
        <option value="text" {{ $tipeNow === 'text' ? 'selected' : '' }}>Teks Singkat</option>
        <option value="textarea" {{ $tipeNow === 'textarea' ? 'selected' : '' }}>Teks Panjang</option>
        <option value="number" {{ $tipeNow === 'number' ? 'selected' : '' }}>Angka</option>
        <option value="date" {{ $tipeNow === 'date' ? 'selected' : '' }}>Tanggal</option>
        <option value="file" {{ $tipeNow === 'file' ? 'selected' : '' }}>Upload File</option>
    </select>
</div>
<div class="mb-3">
    <label class="form-label">Urutan Tampil</label>
    <input type="number" name="urutan" class="form-control" value="{{ old('urutan', $requirement->urutan ?? '') }}">
</div>
<div class="form-check form-switch mb-3">
    <input type="checkbox" name="wajib" class="form-check-input" id="wajib" value="1" {{ old('wajib', $requirement->wajib ?? true) ? 'checked' : '' }}>
    <label class="form-check-label" for="wajib">Wajib diisi</label>
</div>
