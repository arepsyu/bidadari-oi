@extends('layouts.app')
@section('title', 'Data Saya')

@section('content')
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="fw-bold mb-0">Kelengkapan Data: {{ $filled }} / {{ $total }}</h6>
            <span class="fw-bold text-bo-primary">{{ $progress }}%</span>
        </div>
        <div class="progress">
            <div class="progress-bar" style="width: {{ $progress }}%"></div>
        </div>
    </div>
</div>

@forelse($pertanyaans as $klasterKey => $groupByKlaster)
    @php $klasterNama = explode('|', $klasterKey)[1]; @endphp
    <div class="card mb-3">
        <div class="card-header bg-white fw-bold text-bo-primary">
            {{ $klasterNama }}
        </div>
        <div class="card-body">
            @foreach($groupByKlaster->groupBy(fn($p) => $p->indikator->nama) as $indikatorNama => $listPertanyaan)
                <div class="mb-3">
                    <h6 class="fw-semibold mb-2">{{ $indikatorNama }}</h6>
                    @foreach($listPertanyaan as $p)
                        @php $sub = $submissions->get($p->id); @endphp
                        <div class="border rounded p-3 mb-2">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="small">{{ $p->teks }}</div>
                                <div class="ms-2">
                                    @if($sub)
                                        <span class="badge {{ $sub->statusBadgeClass() }}">{{ $sub->statusLabel() }}</span>
                                    @else
                                        <span class="badge bg-danger">Belum</span>
                                    @endif
                                </div>
                            </div>

                            @if($sub && $sub->isDitolak() && $sub->catatan_admin)
                                <div class="alert alert-danger py-2 px-3 small mb-2">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    <strong>Perlu revisi:</strong> {{ $sub->catatan_admin }}
                                </div>
                            @endif

                            <form method="POST" action="{{ route('user.submissions.store', $p) }}" enctype="multipart/form-data" class="row g-2 align-items-end">
                                @csrf
                                <div class="col-md-8">
                                    @if($p->tipe === 'file')
                                        <input type="file" name="file" class="form-control form-control-sm">
                                        @if($sub && $sub->file_path)
                                            <div class="small mt-1">
                                                File saat ini:
                                                <a href="{{ asset($sub->file_path) }}" target="_blank">{{ $sub->file_original_name }}</a>
                                            </div>
                                        @endif
                                    @elseif($p->tipe === 'textarea')
                                        <textarea name="value" class="form-control form-control-sm" rows="2">{{ $sub->value ?? '' }}</textarea>
                                    @elseif($p->tipe === 'date')
                                        <input type="date" name="value" class="form-control form-control-sm" value="{{ $sub->value ?? '' }}">
                                    @elseif($p->tipe === 'number')
                                        <input type="number" name="value" class="form-control form-control-sm" value="{{ $sub->value ?? '' }}">
                                    @else
                                        <input type="text" name="value" class="form-control form-control-sm" value="{{ $sub->value ?? '' }}">
                                    @endif
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-sm btn-accent w-100">
                                        <i class="bi bi-cloud-upload"></i> Simpan
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
@empty
    <div class="text-center text-muted py-5">Belum ada pertanyaan yang ditugaskan untuk akun Anda. Hubungi admin.</div>
@endforelse
@endsection
