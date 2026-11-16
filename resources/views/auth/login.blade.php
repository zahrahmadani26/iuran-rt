<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Sistem Iuran RT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: linear-gradient(135deg, #3730a3 0%, #1e1b4b 100%);
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-wrapper { width: 100%; max-width: 440px; }
        .login-card {
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.25);
            overflow: hidden;
            animation: fadeUp .5s ease-out;
        }
        @keyframes fadeUp {
            from { opacity:0; transform: translateY(20px); }
            to   { opacity:1; transform: translateY(0); }
        }
        .login-header {
            background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
            padding: 36px 32px 28px;
            text-align: center;
            color: #fff;
        }
        .login-icon {
            width: 72px; height: 72px;
            background: rgba(255,255,255,.15);
            border-radius: 20px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 32px;
            margin-bottom: 16px;
        }
        .login-header h4 { font-weight: 800; font-size: 1.4rem; margin-bottom: 4px; }
        .login-header p  { opacity: .8; font-size: .9rem; margin: 0; }
        .login-body { padding: 32px; }
        .form-label { font-weight: 600; font-size: .85rem; color: #374151; margin-bottom: 6px; }
        .form-control {
            border-radius: 10px;
            border: 1.5px solid #e5e7eb;
            padding: 11px 14px;
            font-size: .9rem;
            transition: all .2s;
        }
        .form-control:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79,70,229,.12);
        }
        .btn-login {
            background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 700;
            font-size: .95rem;
            color: #fff;
            width: 100%;
            transition: all .25s;
            letter-spacing: .3px;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(79,70,229,.35);
            color: #fff;
        }
        .demo-box {
            background: #f8f7ff;
            border: 1px solid #e0e7ff;
            border-radius: 12px;
            padding: 16px;
            font-size: .82rem;
        }
        .demo-box .demo-title { font-weight: 700; color: #4f46e5; margin-bottom: 8px; }
        .demo-row { display: flex; justify-content: space-between; padding: 3px 0; color: #4b5563; }
        .demo-row .role { font-weight: 600; color: #374151; }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-header">
                <div class="login-icon">
                    <i class="fas fa-home"></i>
                </div>
                <h4>Sistem Iuran RT</h4>
                <p>Silakan masuk untuk melanjutkan</p>
            </div>
            <div class="login-body">
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Alamat Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0" style="border-radius:10px 0 0 10px; border:1.5px solid #e5e7eb;">
                                <i class="fas fa-envelope text-muted" style="font-size:.85rem;"></i>
                            </span>
                            <input type="email" name="email"
                                   class="form-control border-start-0 @error('email') is-invalid @enderror"
                                   style="border-radius:0 10px 10px 0;"
                                   value="{{ old('email') }}" placeholder="admin@rt.com" required autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0" style="border-radius:10px 0 0 10px; border:1.5px solid #e5e7eb;">
                                <i class="fas fa-lock text-muted" style="font-size:.85rem;"></i>
                            </span>
                            <input type="password" name="password"
                                   class="form-control border-start-0 @error('password') is-invalid @enderror"
                                   style="border-radius:0 10px 10px 0;"
                                   placeholder="••••••••" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div class="form-check mb-0">
                            <input type="checkbox" name="remember" class="form-check-input" id="remember">
                            <label class="form-check-label small" for="remember" style="color:#6b7280;">Ingat saya</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-login mb-3">
                        <i class="fas fa-sign-in-alt me-2"></i> Masuk
                    </button>
                </form>

                <hr class="my-4">

                <div class="demo-box">
                    <div class="demo-title"><i class="fas fa-info-circle me-1"></i> Akun Demo</div>
                    <div class="demo-row">
                        <span class="role">Admin</span>
                        <span>admin@rt.com / password</span>
                    </div>
                    <div class="demo-row">
                        <span class="role">Bendahara</span>
                        <span>bendahara@rt.com / password</span>
                    </div>
                    <div class="demo-row">
                        <span class="role">Warga</span>
                        <span>warga@rt.com / password</span>
                    </div>
                </div>
            </div>
        </div>
        <p class="text-center text-white-50 mt-4 small">© {{ date('Y') }} Sistem Iuran RT. All rights reserved.</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
