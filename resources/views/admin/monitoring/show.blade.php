@extends('layouts.app')
@section('title', 'Detail Data - ' . $user->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <a href="{{ route('admin.monitoring.index') }}" class="btn btn-sm btn-light">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
    <div>
        <a href="{{ route('admin.monitoring.export.user-excel', $user) }}" class="btn btn-sm btn-accent">
            <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
        </a>
        <a href="{{ route('admin.monitoring.export.user-pdf', $user) }}" class="btn btn-sm btn-primary">
            <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
        </a>
        <a href="{{ route('admin.monitoring.download-zip', $user) }}" class="btn btn-sm btn-outline-dark">
            <i class="bi bi-file-earmark-zip me-1"></i> Download Semua Dokumen (ZIP)
        </a>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <h6 class="fw-bold mb-1">{{ $user->organisasi ?? '-' }}</h6>
        <div class="text-muted small">{{ $user->name }} &middot; {{ $user->username }} &middot; {{ $user->kategoriLabel() }}</div>
    </div>
</div>

@forelse($pertanyaans as $klasterKey => $groupByKlaster)
    @php $klasterNama = explode('|', $klasterKey)[1]; @endphp
    <div class="card mb-3">
        <div class="card-header bg-white fw-bold text-bo-primary">{{ $klasterNama }}</div>
        <div class="card-body p-0">
            <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Indikator / Pertanyaan</th>
                        <th>Status</th>
                        <th>Isi / File</th>
                        <th>Update</th>
                        <th class="text-end pe-3">Verifikasi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($groupByKlaster as $p)
                        @php $sub = $submissions->get($p->id); @endphp
                        <tr>
                            <td class="ps-3" style="max-width: 260px;">
                                <div class="small text-muted">{{ $p->indikator->nama }}</div>
                                <div class="small">{{ $p->teks }}</div>
                            </td>
                            <td>
                                @if($sub)
                                    <span class="badge {{ $sub->statusBadgeClass() }}">{{ $sub->statusLabel() }}</span>
                                    @if($sub->isDitolak() && $sub->catatan_admin)
                                        <div class="small text-danger mt-1">"{{ $sub->catatan_admin }}"</div>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">Belum Diisi</span>
                                @endif
                            </td>
                            <td>
                                @if($sub && $sub->value)
                                    <div>{{ \Illuminate\Support\Str::limit($sub->value, 40) }}</div>
                                @endif
                                @if($sub && ($p->tipe === 'file' || $p->wajib_lampiran) && $sub->file_path)
                                    <a href="{{ asset($sub->file_path) }}" target="_blank">
                                        <i class="bi bi-file-earmark-arrow-down"></i> {{ \Illuminate\Support\Str::limit($sub->file_original_name, 25) }}
                                    </a>
                                @endif
                                @if(!$sub)
                                    <span class="text-muted">-</span>
                                @endif
                                @if($sub && $sub->histories->count() > 0)
                                    <br>
                                    <a href="{{ route('admin.submissions.history', $sub) }}" class="small">
                                        <i class="bi bi-clock-history"></i> Riwayat ({{ $sub->histories->count() }})
                                    </a>
                                @endif
                            </td>
                            <td>
                                <span class="small">{{ $sub?->updated_at?->format('d M Y H:i') ?? '-' }}</span>
                            </td>
                            <td class="text-end pe-3">
                                @if($sub)
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            Aksi
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end p-3" style="min-width: 280px;">
                                            <form method="POST" action="{{ route('admin.submissions.verify', $sub) }}" class="mb-2">
                                                @csrf
                                                <input type="hidden" name="status" value="disetujui">
                                                <button type="submit" class="btn btn-sm btn-success w-100">
                                                    <i class="bi bi-check-circle"></i> Setujui
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.submissions.verify', $sub) }}">
                                                @csrf
                                                <input type="hidden" name="status" value="ditolak">
                                                <textarea name="catatan_admin" class="form-control form-control-sm mb-2" rows="2" placeholder="Alasan penolakan / revisi yang diminta..." required></textarea>
                                                <button type="submit" class="btn btn-sm btn-danger w-100">
                                                    <i class="bi bi-x-circle"></i> Tolak & Minta Revisi
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>
    </div>
@empty
    <div class="text-center text-muted py-5">Belum ada pertanyaan yang ditugaskan untuk akun ini.</div>
@endforelse
@endsection
