@extends('layouts.app')
@section('title', 'Dashboard Monitoring')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h6 class="text-muted mb-0">Ringkasan kelengkapan data seluruh OPD, Kecamatan & Desa</h6>
    <div>
        <button type="button" class="btn btn-outline-warning" id="btnReminderMassal">
            <i class="bi bi-megaphone me-1"></i> Reminder Massal
        </button>
        <a href="{{ route('admin.monitoring.export.excel') }}" class="btn btn-accent">
            <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
        </a>
        <a href="{{ route('admin.monitoring.export.pdf') }}" class="btn btn-primary">
            <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card blue p-3">
            <div class="small opacity-75">Total Akun</div>
            <div class="fs-3 fw-bold">{{ $totalUsers }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card green p-3">
            <div class="small opacity-75">Total Pertanyaan KLA</div>
            <div class="fs-3 fw-bold">{{ $totalPertanyaan }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3" style="border-radius:14px; color:#fff; background: linear-gradient(135deg, #b9770e, #f0a500);">
            <div class="small opacity-75">Menunggu Verifikasi</div>
            <div class="fs-3 fw-bold">{{ $menungguVerifikasi }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card green p-3">
            <div class="small opacity-75">Kelengkapan Keseluruhan</div>
            <div class="fs-3 fw-bold">{{ $completionRate }}%</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold">Status Akun</div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="chartStatus" style="max-height: 220px;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold">Rata-rata Kelengkapan per Kategori Akun</div>
            <div class="card-body">
                <canvas id="chartKategori" style="max-height: 220px;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white fw-semibold">Progres Kelengkapan Data per Akun</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Organisasi / Akun</th>
                    <th>Kategori</th>
                    <th>Data Terisi</th>
                    <th>Menunggu Verifikasi</th>
                    <th style="width: 200px;">Progres</th>
                    <th class="text-end pe-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td class="ps-3">
                        <div class="fw-semibold">{{ $user->organisasi ?? $user->name }}</div>
                        <div class="small text-muted">{{ $user->username }}</div>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">{{ $user->kategoriLabel() }}</span>
                    </td>
                    <td>{{ $user->submissions_count }} / {{ $user->relevan_count }}</td>
                    <td>
                        @if($user->menunggu_count > 0)
                            <span class="badge bg-warning text-dark">{{ $user->menunggu_count }} pending</span>
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </td>
                    <td>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ $user->progress }}%"></div>
                        </div>
                        <div class="small text-muted mt-1">{{ $user->progress }}%</div>
                    </td>
                    <td class="text-end pe-3">
                        <a href="{{ route('admin.monitoring.show', $user) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('admin.monitoring.export.user-excel', $user) }}" class="btn btn-sm btn-outline-success">
                            <i class="bi bi-file-earmark-excel"></i>
                        </a>
                        <a href="{{ route('admin.monitoring.export.user-pdf', $user) }}" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-file-earmark-pdf"></i>
                        </a>
                        <a href="{{ route('admin.monitoring.download-zip', $user) }}" class="btn btn-sm btn-outline-dark" title="Download semua dokumen (ZIP)">
                            <i class="bi bi-file-earmark-zip"></i>
                        </a>
                        @if($user->progress < 100)
                            <button type="button" class="btn btn-sm btn-outline-warning btn-reminder"
                                data-organisasi="{{ $user->organisasi ?? $user->name }}"
                                data-progress="{{ $user->progress }}"
                                data-terisi="{{ $user->submissions_count }}"
                                data-relevan="{{ $user->relevan_count }}"
                                title="Kirim reminder">
                                <i class="bi bi-megaphone"></i>
                            </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada akun user.</td></tr>
                @endforelse
            </tbody>
        </table>
            </div>
    </div>
</div>

<!-- Modal Reminder per akun -->
<div class="modal fade" id="reminderModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-bold"><i class="bi bi-megaphone text-warning"></i> Teks Reminder</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <textarea class="form-control" id="reminderText" rows="7" readonly></textarea>
                <div class="form-text">Copy teks ini, tinggal kirim manual lewat WhatsApp/email ke akun yang bersangkutan.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btnCopyReminder">
                    <i class="bi bi-clipboard"></i> Salin Teks
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Reminder Massal -->
<div class="modal fade" id="reminderMassalModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-bold"><i class="bi bi-megaphone text-warning"></i> Reminder Massal (Akun Belum Lengkap)</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <textarea class="form-control" id="reminderMassalText" rows="14" readonly></textarea>
                <div class="form-text">Daftar semua akun yang datanya belum 100%, diurutkan dari yang paling rendah.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btnCopyReminderMassal">
                    <i class="bi bi-clipboard"></i> Salin Teks
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    new Chart(document.getElementById('chartStatus'), {
        type: 'doughnut',
        data: {
            labels: ['Sudah Lengkap (100%)', 'Belum Lengkap'],
            datasets: [{
                data: [{{ $sudahLengkap }}, {{ $belumLengkap }}],
                backgroundColor: ['#409f74', '#e0e0e0'],
                borderWidth: 0
            }]
        },
        options: { plugins: { legend: { position: 'bottom' } } }
    });

    new Chart(document.getElementById('chartKategori'), {
        type: 'bar',
        data: {
            labels: [@foreach($perKategori as $kat => $val) '{{ ucfirst($kat) }} ({{ $val['jumlah_akun'] }} akun)', @endforeach],
            datasets: [{
                label: 'Rata-rata Kelengkapan (%)',
                data: [@foreach($perKategori as $kat => $val) {{ $val['rata_progress'] }}, @endforeach],
                backgroundColor: '#005093',
                borderRadius: 6
            }]
        },
        options: {
            scales: { y: { beginAtZero: true, max: 100 } },
            plugins: { legend: { display: false } }
        }
    });

    // ==== Fitur Reminder ====
    const reminderModalEl = document.getElementById('reminderModal');
    const reminderText = document.getElementById('reminderText');

    function buatTeksReminder(organisasi, progress, terisi, relevan) {
        return 'Halo ' + organisasi + ',\n\n'
            + 'Ini reminder dari Admin BIDADARI OI. Kelengkapan pengisian data KLA untuk *' + organisasi + '* '
            + 'saat ini baru mencapai *' + progress + '%* (' + terisi + ' dari ' + relevan + ' data).\n\n'
            + 'Mohon segera dilengkapi ya, terima kasih 🙏';
    }

    document.querySelectorAll('.btn-reminder').forEach(function (btn) {
        btn.addEventListener('click', function () {
            reminderText.value = buatTeksReminder(
                btn.dataset.organisasi,
                btn.dataset.progress,
                btn.dataset.terisi,
                btn.dataset.relevan
            );
            bootstrap.Modal.getOrCreateInstance(reminderModalEl).show();
        });
    });

    document.getElementById('btnCopyReminder').addEventListener('click', function () {
        reminderText.select();
        navigator.clipboard.writeText(reminderText.value);
        this.innerHTML = '<i class="bi bi-check2"></i> Tersalin!';
        setTimeout(() => this.innerHTML = '<i class="bi bi-clipboard"></i> Salin Teks', 2000);
    });

    // ==== Reminder Massal ====
    const dataAkunBelumLengkap = [
        @foreach($users->where('progress', '<', 100)->sortBy('progress') as $u)
            {
                organisasi: @json($u->organisasi ?? $u->name),
                progress: {{ $u->progress }},
                terisi: {{ $u->submissions_count }},
                relevan: {{ $u->relevan_count }},
            },
        @endforeach
    ];

    document.getElementById('btnReminderMassal').addEventListener('click', function () {
        const massalModal = new bootstrap.Modal(document.getElementById('reminderMassalModal'));
        const massalText = document.getElementById('reminderMassalText');

        if (dataAkunBelumLengkap.length === 0) {
            massalText.value = 'Semua akun sudah 100% lengkap. Gak ada yang perlu diingetin. 🎉';
        } else {
            let teks = 'REMINDER KELENGKAPAN DATA KLA - ' + new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) + '\n\n';
            teks += 'Berikut daftar organisasi yang datanya masih belum lengkap:\n\n';
            dataAkunBelumLengkap.forEach(function (u, i) {
                teks += (i + 1) + '. ' + u.organisasi + ' - ' + u.progress + '% (' + u.terisi + '/' + u.relevan + ')\n';
            });
            teks += '\nMohon segera dilengkapi datanya. Terima kasih 🙏';
            massalText.value = teks;
        }

        massalModal.show();
    });

    document.getElementById('btnCopyReminderMassal').addEventListener('click', function () {
        const massalText = document.getElementById('reminderMassalText');
        massalText.select();
        navigator.clipboard.writeText(massalText.value);
        this.innerHTML = '<i class="bi bi-check2"></i> Tersalin!';
        setTimeout(() => this.innerHTML = '<i class="bi bi-clipboard"></i> Salin Teks', 2000);
    });
</script>
@endsection
