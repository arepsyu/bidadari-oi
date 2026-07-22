<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BIDADARI OI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --bo-primary: #005093;
            --bo-secondary: #1886ca;
            --bo-accent: #409f74;
        }
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--bo-primary), var(--bo-secondary));
            font-family: 'Segoe UI', system-ui, sans-serif;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            border: none;
            border-radius: 18px;
            box-shadow: 0 15px 40px rgba(0,0,0,.25);
        }
        .login-card img { width: 90px; height: 90px; object-fit: contain; }
        .btn-primary {
            background-color: var(--bo-primary);
            border-color: var(--bo-primary);
        }
        .btn-primary:hover {
            background-color: var(--bo-secondary);
            border-color: var(--bo-secondary);
        }
    </style>
</head>
<body>
    <div class="card login-card p-4">
        <div class="text-center mb-3">
            <img src="{{ asset('images/logo.png') }}" alt="Logo BIDADARI OI">
            <h5 class="fw-bold mt-2 mb-0" style="color: var(--bo-primary);">BIDADARI OI</h5>
            <div class="small text-muted">Bank Informasi Data Kabupaten Layak Anak Terintegrasi Ogan Ilir</div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger py-2">
                @foreach ($errors->all() as $error)
                    <div class="small">{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login.attempt') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                <label class="form-check-label" for="remember">Ingat saya</label>
            </div>
            <button type="submit" class="btn btn-primary w-100">Masuk</button>
        </form>
    </div>
</body>
</html>
