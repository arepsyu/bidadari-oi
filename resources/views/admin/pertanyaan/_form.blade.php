<div class="mb-3">
    <label class="form-label">Indikator</label>
    <select name="indikator_id" class="form-select" required>
        <option value="">-- Pilih Indikator --</option>
        @foreach($klasters as $k)
            <optgroup label="{{ $k->nama }}">
                @foreach($k->indikators as $ind)
                    <option value="{{ $ind->id }}" {{ old('indikator_id', $pertanyaan->indikator_id ?? '') == $ind->id ? 'selected' : '' }}>
                        {{ $ind->kode }} - {{ $ind->nama }}
                    </option>
                @endforeach
            </optgroup>
        @endforeach
    </select>
    @error('indikator_id') <div class="text-danger small">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">Kode (opsional)</label>
    <input type="text" name="kode" class="form-control" placeholder="Contoh: Pertanyaan 1" value="{{ old('kode', $pertanyaan->kode ?? '') }}">
</div>

<div class="mb-3">
    <label class="form-label">Teks Pertanyaan</label>
    <textarea name="teks" class="form-control" rows="3" required>{{ old('teks', $pertanyaan->teks ?? '') }}</textarea>
    @error('teks') <div class="text-danger small">{{ $message }}</div> @enderror
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Tipe Input</label>
        @php $tipeNow = old('tipe', $pertanyaan->tipe ?? 'file'); @endphp
        <select name="tipe" class="form-select" required>
            <option value="file" {{ $tipeNow === 'file' ? 'selected' : '' }}>Upload File</option>
            <option value="text" {{ $tipeNow === 'text' ? 'selected' : '' }}>Teks Singkat</option>
            <option value="textarea" {{ $tipeNow === 'textarea' ? 'selected' : '' }}>Teks Panjang</option>
            <option value="number" {{ $tipeNow === 'number' ? 'selected' : '' }}>Angka</option>
            <option value="date" {{ $tipeNow === 'date' ? 'selected' : '' }}>Tanggal</option>
        </select>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Urutan Tampil</label>
        <input type="number" name="urutan" class="form-control" value="{{ old('urutan', $pertanyaan->urutan ?? 0) }}">
    </div>
</div>

<div class="form-check form-switch mb-3">
    <input type="checkbox" name="wajib" class="form-check-input" id="wajib" value="1" {{ old('wajib', $pertanyaan->wajib ?? true) ? 'checked' : '' }}>
    <label class="form-check-label" for="wajib">Wajib diisi</label>
</div>

<div class="form-check form-switch mb-3">
    <input type="checkbox" name="wajib_lampiran" class="form-check-input" id="wajib_lampiran" value="1" {{ old('wajib_lampiran', $pertanyaan->wajib_lampiran ?? false) ? 'checked' : '' }}>
    <label class="form-check-label" for="wajib_lampiran">
        Butuh lampiran dokumen tambahan juga
    </label>
    <div class="form-text">
        Aktifkan kalau pertanyaan ini butuh 2 jawaban sekaligus — misal jawaban teks/angka
        DITAMBAH upload dokumen pendukung (matriks, dll). Gak berlaku kalau Tipe Input di atas
        udah "Upload File".
    </div>
</div>

<hr>
<label class="form-label fw-semibold">Siapa yang harus mengisi pertanyaan ini?</label>

<div class="form-check mb-2">
    <input type="checkbox" name="untuk_kecamatan" class="form-check-input" id="untuk_kecamatan" value="1" {{ old('untuk_kecamatan', $pertanyaan->untuk_kecamatan ?? false) ? 'checked' : '' }}>
    <label class="form-check-label" for="untuk_kecamatan">Semua akun berkategori <strong>Kecamatan</strong></label>
</div>
<div class="form-check mb-3">
    <input type="checkbox" name="untuk_desa" class="form-check-input" id="untuk_desa" value="1" {{ old('untuk_desa', $pertanyaan->untuk_desa ?? false) ? 'checked' : '' }}>
    <label class="form-check-label" for="untuk_desa">Semua akun berkategori <strong>Desa</strong></label>
</div>

<div class="mb-3">
    <label class="form-label">OPD/Dinas Terkait (bisa pilih lebih dari satu)</label>
    <div class="border rounded p-2" style="max-height: 220px; overflow-y: auto;">
        @foreach($opds as $opd)
            <div class="form-check">
                <input type="checkbox" name="opds[]" value="{{ $opd->id }}" class="form-check-input" id="opd{{ $opd->id }}"
                    {{ in_array($opd->id, old('opds', $selectedOpds ?? [])) ? 'checked' : '' }}>
                <label class="form-check-label" for="opd{{ $opd->id }}">{{ $opd->nama }}</label>
            </div>
        @endforeach
    </div>
</div>
