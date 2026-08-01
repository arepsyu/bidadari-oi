<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - BIDADARI OI</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root {
            --bo-primary: #005093;
            --bo-secondary: #1886ca;
            --bo-accent: #409f74;
            --bo-light: #eaf4fb;
        }
        body {
            background-color: var(--bo-light);
            font-family: 'Segoe UI', system-ui, sans-serif;
        }
        .bo-sidebar {
            min-height: 100vh;
            height: 100vh;
            position: sticky;
            top: 0;
            background: linear-gradient(180deg, var(--bo-primary) 0%, var(--bo-secondary) 100%);
            color: #fff;
            overflow: hidden;
        }
        .bo-sidebar .brand {
            padding: 1.25rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,.15);
            flex-shrink: 0;
        }
        .bo-sidebar .nav-scroll {
            flex: 1 1 auto;
            overflow-y: auto;
        }
        .bo-sidebar .logout-area {
            flex-shrink: 0;
        }
        .bo-sidebar .brand img { width: 42px; height: 42px; object-fit: contain; background: #fff; border-radius: 50%; padding: 3px; }
        .bo-sidebar .nav-link {
            color: rgba(255,255,255,.85);
            border-radius: 8px;
            margin: 2px 10px;
            padding: .6rem .9rem;
        }
        .bo-sidebar .nav-link.active, .bo-sidebar .nav-link:hover {
            background: rgba(255,255,255,.15);
            color: #fff;
        }
        .bo-topbar {
            background: #fff;
            border-bottom: 1px solid #e3edf5;
        }
        .card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(0,80,147,.08);
        }
        .btn-primary {
            background-color: var(--bo-primary);
            border-color: var(--bo-primary);
        }
        .btn-primary:hover {
            background-color: var(--bo-secondary);
            border-color: var(--bo-secondary);
        }
        .btn-accent {
            background-color: var(--bo-accent);
            border-color: var(--bo-accent);
            color: #fff;
        }
        .btn-accent:hover { color: #fff; opacity: .9; }
        .text-bo-primary { color: var(--bo-primary); }
        .badge-wajib { background-color: var(--bo-accent); }
        .stat-card { border-radius: 14px; color: #fff; }
        .stat-card.blue { background: linear-gradient(135deg, var(--bo-primary), var(--bo-secondary)); }
        .stat-card.green { background: linear-gradient(135deg, #2e8b5f, var(--bo-accent)); }
        .progress { height: 10px; border-radius: 6px; }
        .progress-bar { background-color: var(--bo-accent); }

        /* ==== Mobile responsive sidebar ==== */
        .bo-hamburger { display: none; }
        .bo-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.4);
            z-index: 1030;
        }
        @media (max-width: 991.98px) {
            .bo-sidebar {
                position: fixed;
                top: 0;
                left: 0;
                z-index: 1040;
                width: 260px;
                max-width: 80vw;
                transform: translateX(-100%);
                transition: transform .25s ease-in-out;
            }
            .bo-sidebar.show {
                transform: translateX(0);
            }
            .bo-hamburger {
                display: inline-flex;
            }
            .bo-backdrop.show {
                display: block;
            }
            .bo-topbar h5 {
                font-size: 1rem;
            }
            .bo-topbar .text-end .small {
                display: none;
            }
        }
    </style>
</head>
<body>
<div class="bo-backdrop" id="boBackdrop"></div>
<div class="d-flex">
    <nav class="bo-sidebar d-flex flex-column flex-shrink-0" id="boSidebar" style="width: 260px;">
        <div class="brand d-flex align-items-center gap-2">
            <img src="{{ asset('images/logo.png') }}" alt="Logo">
            <div>
                <div class="fw-bold" style="font-size: 1rem; line-height:1.1;">BIDADARI OI</div>
                <div style="font-size:.65rem; opacity:.85; line-height:1.3;">Bank Informasi Data Kabupaten Layak Anak Terintegrasi Ogan Ilir</div>
            </div>
        </div>
        <div class="nav flex-column mt-2 nav-scroll">
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.monitoring.index') }}" class="nav-link {{ request()->routeIs('admin.monitoring.*') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard Monitoring
                </a>
                <a href="{{ route('admin.monitoring-desa.index') }}" class="nav-link {{ request()->routeIs('admin.monitoring-desa.*') ? 'active' : '' }}">
                    <i class="bi bi-map me-2"></i> Monitoring Desa
                </a>
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="bi bi-people me-2"></i> Kelola Akun
                </a>
                <a href="{{ route('admin.pertanyaan.index') }}" class="nav-link {{ request()->routeIs('admin.pertanyaan.*') ? 'active' : '' }}">
                    <i class="bi bi-list-check me-2"></i> Kelola Pertanyaan KLA
                </a>
                <a href="{{ route('admin.opd.index') }}" class="nav-link {{ request()->routeIs('admin.opd.*') ? 'active' : '' }}">
                    <i class="bi bi-building me-2"></i> Kelola Master OPD
                </a>
            @else
                <a href="{{ route('user.submissions.index') }}" class="nav-link {{ request()->routeIs('user.submissions.*') ? 'active' : '' }}">
                    <i class="bi bi-cloud-upload me-2"></i> Data Saya
                </a>
                @if(auth()->user()->isKecamatan())
                    <a href="{{ route('user.desa.monitoring') }}" class="nav-link {{ request()->routeIs('user.desa.monitoring') ? 'active' : '' }}">
                        <i class="bi bi-bar-chart-steps me-2"></i> Monitoring Desa
                    </a>
                    <a href="{{ route('user.desa.pilih') }}" class="nav-link {{ request()->routeIs('user.desa.pilih') || request()->routeIs('user.desa.show') ? 'active' : '' }}">
                        <i class="bi bi-pencil-square me-2"></i> Input Data Desa
                    </a>
                @endif
            @endif
        </div>
        <div class="p-3 logout-area">
            <a href="{{ route('profile.password.edit') }}" class="btn btn-sm btn-outline-light w-100 mb-2">
                <i class="bi bi-key"></i> Ganti Password
            </a>
            <form action="{{ route('logout') }}" method="POST" id="logoutForm">
                @csrf
                <button class="btn btn-sm btn-light w-100" type="submit">
                    <i class="bi bi-box-arrow-right me-1"></i> Keluar
                </button>
            </form>
        </div>
    </nav>

    <div class="flex-grow-1">
        <div class="bo-topbar d-flex align-items-center justify-content-between px-3 px-md-4 py-3">
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-outline-primary bo-hamburger" id="boHamburgerBtn" type="button">
                    <i class="bi bi-list"></i>
                </button>
                <h5 class="mb-0 text-bo-primary fw-bold">@yield('title', 'Dashboard')</h5>
            </div>
            <div class="text-end">
                <div class="fw-semibold">{{ auth()->user()->name }}</div>
                <div class="small text-muted">{{ auth()->user()->organisasi ?? ucfirst(auth()->user()->role) }}</div>
            </div>
        </div>

        <div class="p-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>
</div>
<div class="modal fade" id="filePreviewModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-bold text-truncate" id="filePreviewTitle">Preview Dokumen</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" id="filePreviewBody" style="min-height: 400px; max-height: 75vh; overflow: auto;">
            </div>
            <div class="modal-footer">
                <a href="#" id="filePreviewDownloadLink" target="_blank" class="btn btn-primary">
                    <i class="bi bi-download"></i> Download File
                </a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="idleWarningModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <i class="bi bi-clock-history text-warning" style="font-size: 2.5rem;"></i>
                <h6 class="fw-bold mt-3 mb-2">Sesi Anda akan berakhir</h6>
                <p class="text-muted small mb-1">Anda gak aktif beberapa saat. Demi keamanan, sesi bakal otomatis logout dalam:</p>
                <div class="fs-3 fw-bold text-bo-primary mb-3" id="idleCountdown">02:00</div>
                <button type="button" class="btn btn-primary w-100" id="idleStayBtn">
                    <i class="bi bi-check-circle"></i> Tetap Login
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const boSidebar = document.getElementById('boSidebar');
    const boBackdrop = document.getElementById('boBackdrop');
    const boHamburgerBtn = document.getElementById('boHamburgerBtn');

    // ==== Preview dokumen (PDF/gambar) tanpa perlu download dulu ====
    function bidadariPreviewFile(url, filename) {
        const modalEl = document.getElementById('filePreviewModal');
        const modal = new bootstrap.Modal(modalEl);
        const body = document.getElementById('filePreviewBody');
        const title = document.getElementById('filePreviewTitle');
        const downloadLink = document.getElementById('filePreviewDownloadLink');

        title.textContent = filename || 'Preview Dokumen';
        downloadLink.href = url;

        const ext = (filename || url).split('.').pop().toLowerCase();

        if (ext === 'pdf') {
            body.innerHTML = '<iframe src="' + url + '" style="width:100%; height:75vh; border:0;"></iframe>';
        } else if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
            body.innerHTML = '<div class="text-center p-3"><img src="' + url + '" style="max-width:100%; max-height:70vh;" alt="Preview"></div>';
        } else {
            body.innerHTML = '<div class="text-center p-5 text-muted">'
                + '<i class="bi bi-file-earmark-x" style="font-size:2.5rem;"></i>'
                + '<p class="mt-3 mb-0">Preview gak didukung buat tipe file ini (' + ext.toUpperCase() + ').</p>'
                + '<p class="small">Klik "Download File" di bawah buat buka filenya.</p>'
                + '</div>';
        }

        modal.show();
    }

    function boOpenSidebar() {
        boSidebar.classList.add('show');
        boBackdrop.classList.add('show');
    }
    function boCloseSidebar() {
        boSidebar.classList.remove('show');
        boBackdrop.classList.remove('show');
    }

    boHamburgerBtn?.addEventListener('click', function () {
        boSidebar.classList.contains('show') ? boCloseSidebar() : boOpenSidebar();
    });
    boBackdrop?.addEventListener('click', boCloseSidebar);

    // Tutup sidebar otomatis pas nav-link di-klik (biar gak nutupin halaman baru di HP)
    boSidebar.querySelectorAll('a.nav-link').forEach(function (link) {
        link.addEventListener('click', function () {
            if (window.innerWidth <= 991.98) {
                boCloseSidebar();
            }
        });
    });

    // ==== Auto-logout kalau idle (gak ada aktivitas) 45 menit ====
    (function () {
        const IDLE_LIMIT_MS = 45 * 60 * 1000; // 45 menit
        const WARNING_BEFORE_MS = 2 * 60 * 1000; // munculin peringatan 2 menit sebelum abis

        const idleModalEl = document.getElementById('idleWarningModal');
        const idleModal = new bootstrap.Modal(idleModalEl);
        const countdownEl = document.getElementById('idleCountdown');
        const stayBtn = document.getElementById('idleStayBtn');

        let idleTimer, warningTimer, countdownInterval;

        function formatCountdown(ms) {
            const totalSeconds = Math.max(0, Math.ceil(ms / 1000));
            const minutes = Math.floor(totalSeconds / 60);
            const seconds = totalSeconds % 60;
            return String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
        }

        function doAutoLogout() {
            document.getElementById('logoutForm').submit();
        }

        function showWarning() {
            let msLeft = WARNING_BEFORE_MS;
            countdownEl.textContent = formatCountdown(msLeft);
            idleModal.show();

            countdownInterval = setInterval(function () {
                msLeft -= 1000;
                countdownEl.textContent = formatCountdown(msLeft);
                if (msLeft <= 0) {
                    clearInterval(countdownInterval);
                }
            }, 1000);
        }

        function resetIdleTimer() {
            clearTimeout(idleTimer);
            clearTimeout(warningTimer);
            clearInterval(countdownInterval);

            if (idleModalEl.classList.contains('show')) {
                idleModal.hide();
            }

            warningTimer = setTimeout(showWarning, IDLE_LIMIT_MS - WARNING_BEFORE_MS);
            idleTimer = setTimeout(doAutoLogout, IDLE_LIMIT_MS);
        }

        stayBtn.addEventListener('click', resetIdleTimer);

        ['mousemove', 'keydown', 'click', 'scroll', 'touchstart'].forEach(function (evt) {
            document.addEventListener(evt, function () {
                // Jangan reset timer kalau modal peringatan lagi kebuka -- biar user
                // sadar & sengaja klik "Tetap Login", bukan ke-reset gara-gara mouse kesenggol.
                if (! idleModalEl.classList.contains('show')) {
                    resetIdleTimer();
                }
            }, { passive: true });
        });

        resetIdleTimer();
    })();
</script>
</body>
</html>
