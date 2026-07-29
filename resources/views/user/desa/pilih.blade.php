@extends('layouts.app')
@section('title', 'Input Data Desa')

@section('content')
<div class="card" style="max-width: 500px;">
    <div class="card-body p-4">
        <h6 class="fw-bold mb-3">Pilih Desa/Kelurahan</h6>
        <p class="text-muted small">Pilih desa/kelurahan yang mau diisi datanya.</p>

        <form method="GET" id="formPilihDesa">
            <div class="mb-3">
                <select name="desa" class="form-select" id="selectDesa" required>
                    <option value="">-- Pilih Desa/Kelurahan --</option>
                    @foreach($desas as $desa)
                        <option value="{{ $desa->id }}">{{ $desa->nama }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100" onclick="return arahkanKeDesa()">
                <i class="bi bi-arrow-right-circle"></i> Buka Form Input
            </button>
        </form>
    </div>
</div>

<script>
    function arahkanKeDesa() {
        const select = document.getElementById('selectDesa');
        if (!select.value) return false;
        window.location.href = "{{ url('data-desa/input') }}/" + select.value;
        return false;
    }
</script>
@endsection
