<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - BIDADARI OI</title>
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
            background: linear-gradient(180deg, var(--bo-primary) 0%, var(--bo-secondary) 100%);
            color: #fff;
        }
        .bo-sidebar .brand {
            padding: 1.25rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,.15);
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
    </style>
</head>
<body>
<div class="d-flex">
    <nav class="bo-sidebar d-flex flex-column flex-shrink-0" style="width: 260px;">
        <div class="brand d-flex align-items-center gap-2">
            <img src="{{ asset('images/logo.png') }}" alt="Logo">
            <div>
                <div class="fw-bold" style="font-size: 1rem; line-height:1.1;">BIDADARI OI</div>
                <div style="font-size:.7rem; opacity:.85;">Bank Data Kab. Layak Anak</div>
            </div>
        </div>
        <div class="nav flex-column mt-2">
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.monitoring.index') }}" class="nav-link {{ request()->routeIs('admin.monitoring.*') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard Monitoring
                </a>
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="bi bi-people me-2"></i> Kelola Akun User
                </a>
                <a href="{{ route('admin.requirements.index') }}" class="nav-link {{ request()->routeIs('admin.requirements.*') ? 'active' : '' }}">
                    <i class="bi bi-list-check me-2"></i> Kelola Jenis Data
                </a>
            @else
                <a href="{{ route('user.submissions.index') }}" class="nav-link {{ request()->routeIs('user.submissions.*') ? 'active' : '' }}">
                    <i class="bi bi-cloud-upload me-2"></i> Data Saya
                </a>
            @endif
        </div>
        <div class="mt-auto p-3">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn btn-sm btn-light w-100" type="submit">
                    <i class="bi bi-box-arrow-right me-1"></i> Keluar
                </button>
            </form>
        </div>
    </nav>

    <div class="flex-grow-1">
        <div class="bo-topbar d-flex align-items-center justify-content-between px-4 py-3">
            <h5 class="mb-0 text-bo-primary fw-bold">@yield('title', 'Dashboard')</h5>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
