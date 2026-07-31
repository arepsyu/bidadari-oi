@extends('layouts.app')
@section('title', 'Riwayat Perubahan Data')

@section('content')
<a href="{{ route('admin.monitoring.show', $submission->user) }}" class="btn btn-sm btn-light mb-3">
    <i class="bi bi-arrow-left"></i> Kembali ke Detail Akun
</a>

<div class="card mb-3">
    <div class="card-body">
        <div class="small text-muted">{{ $submission->pertanyaan->indikator->klaster->nama }} &rsaquo; {{ $submission->pertanyaan->indikator->nama }}</div>
        <h6 class="fw-bold mb-2">{{ $submission->pertanyaan->teks }}</h6>
        <div class="small text-muted">Organisasi: {{ $submission->user->organisasi ?? $submission->user->name }}</div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header bg-white fw-semibold">
        <i class="bi bi-star-fill text-warning"></i> Versi Saat Ini
    </div>
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                @if($submission->pertanyaan->tipe === 'file' && $submission->file_path)
                    <a href="javascript:void(0)" onclick="bidadariPreviewFile('{{ asset($submission->file_path) }}', '{{ addslashes($submission->file_original_name) }}')">
                        <i class="bi bi-eye"></i> {{ $submission->file_original_name }}
                    </a>
                @else
                    {{ $submission->value }}
                @endif
                <div class="small text-muted mt-1">Diupload: {{ $submission->updated_at->format('d M Y H:i') }}</div>
            </div>
            <span class="badge {{ $submission->statusBadgeClass() }}">{{ $submission->statusLabel() }}</span>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white fw-semibold">Versi Sebelumnya ({{ $submission->histories->count() }})</div>
    <div class="card-body p-0">
        @forelse($submission->histories as $h)
            <div class="border-bottom p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        @if($submission->pertanyaan->tipe === 'file' && $h->file_path)
                            <a href="javascript:void(0)" onclick="bidadariPreviewFile('{{ asset($h->file_path) }}', '{{ addslashes($h->file_original_name) }}')">
                                <i class="bi bi-eye"></i> {{ $h->file_original_name }}
                            </a>
                        @else
                            <span>{{ $h->value ?? '-' }}</span>
                        @endif
                        <div class="small text-muted mt-1">
                            Diganti pada: {{ $h->diganti_at->format('d M Y H:i') }}
                        </div>
                    </div>
                    @if($h->status_saat_itu)
                        <span class="badge bg-secondary">Status saat itu: {{ ucfirst($h->status_saat_itu) }}</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center text-muted py-4">Belum ada riwayat perubahan — ini upload pertama.</div>
        @endforelse
    </div>
</div>
@endsection
