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

@forelse($requirements as $req)
    @php $sub = $submissions->get($req->id); @endphp
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <h6 class="fw-bold mb-1">
                        {{ $req->judul }}
                        @if($req->wajib)<span class="badge badge-wajib">Wajib</span>@endif
                    </h6>
                    @if($req->deskripsi)
                        <div class="small text-muted mb-2">{{ $req->deskripsi }}</div>
                    @endif
                </div>
                <div>
                    @if($sub)
                        <span class="badge bg-success"><i class="bi bi-check-circle"></i> Terisi</span>
                    @else
                        <span class="badge bg-danger">Belum Diisi</span>
                    @endif
                </div>
            </div>

            <form method="POST" action="{{ route('user.submissions.store', $req) }}" enctype="multipart/form-data" class="row g-2 align-items-end">
                @csrf
                <div class="col-md-8">
                    @if($req->tipe === 'file')
                        <input type="file" name="file" class="form-control">
                        @if($sub && $sub->file_path)
                            <div class="small mt-1">
                                File saat ini:
                                <a href="{{ asset('storage/' . $sub->file_path) }}" target="_blank">{{ $sub->file_original_name }}</a>
                            </div>
                        @endif
                    @elseif($req->tipe === 'textarea')
                        <textarea name="value" class="form-control" rows="2">{{ $sub->value ?? '' }}</textarea>
                    @elseif($req->tipe === 'date')
                        <input type="date" name="value" class="form-control" value="{{ $sub->value ?? '' }}">
                    @elseif($req->tipe === 'number')
                        <input type="number" name="value" class="form-control" value="{{ $sub->value ?? '' }}">
                    @else
                        <input type="text" name="value" class="form-control" value="{{ $sub->value ?? '' }}">
                    @endif
                    @error('value') <div class="text-danger small">{{ $message }}</div> @enderror
                    @error('file') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-accent w-100">
                        <i class="bi bi-cloud-upload"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
@empty
    <div class="text-center text-muted py-5">Belum ada jenis data yang ditentukan oleh admin.</div>
@endforelse
@endsection
