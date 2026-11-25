<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Warga — SI-IURAN RT</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #3730a3 0%, #4f46e5 60%, #6366f1 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 16px;
        }
        .register-box {
            background: white;
            border-radius: 24px;
            padding: 40px;
            width: 100%;
            max-width: 620px;
            box-shadow: 0 20px 60px rgba(0,0,0,.2);
        }
        .register-brand {
            text-align: center;
            margin-bottom: 28px;
        }
        .register-brand .brand-icon {
            width: 60px; height: 60px;
            background: linear-gradient(135deg, #4f46e5, #3730a3);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin: 0 auto 12px;
        }
        .register-brand h1 {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 4px;
        }
        .register-brand p {
            color: #64748b;
            font-size: .9rem;
        }
        .form-label { font-weight: 600; color: #374151; font-size: .9rem; }
        .form-control, .form-select {
            border-radius: 10px;
            border: 1.5px solid #e2e8f0;
            padding: 10px 14px;
            font-size: .95rem;
            transition: border .2s;
        }
        .form-control:focus, .form-select:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79,70,229,.1);
        }
        .btn-daftar {
            background: linear-gradient(135deg, #4f46e5, #3730a3);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 13px;
            font-size: 1rem;
            font-weight: 700;
            width: 100%;
            transition: all .2s;
        }
        .btn-daftar:hover { opacity: .9; transform: translateY(-1px); }
        .info-box {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 12px;
            padding: 14px 16px;
            font-size: .88rem;
            color: #0369a1;
        }
        .section-divider {
            border-top: 1.5px solid #f1f5f9;
            margin: 20px 0;
            position: relative;
        }
        .section-divider span {
            position: absolute;
            top: -11px;
            left: 50%;
            transform: translateX(-50%);
            background: white;
            padding: 0 12px;
            color: #94a3b8;
            font-size: .8rem;
            white-space: nowrap;
        }
    </style>
</head>
<body>
<div class="register-box">
    <div class="register-brand">
        <div class="brand-icon"><i class="fas fa-building-user"></i></div>
        <h1>Daftar Sebagai Warga</h1>
        <p>Isi data diri kamu untuk mendaftar di sistem RT digital</p>
    </div>

    <!-- Alert Info -->
    <div class="info-box mb-4">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Penting:</strong> Setelah mendaftar, akun kamu akan menunggu verifikasi dari Ketua RT (Super Admin). 
        Kamu akan bisa login setelah akun disetujui.
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 rounded-3 mb-4">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success border-0 rounded-3 mb-4">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('register.warga.store') }}" method="POST">
        @csrf
        <h6 class="fw-bold text-muted mb-3" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;">
            <i class="fas fa-user me-2 text-primary"></i>Data Akun
        </h6>
        <div class="row g-3 mb-3">
            <div class="col-12">
                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Sesuai KTP" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="email@contoh.com" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">No. Telepon <span class="text-danger">*</span></label>
                <input type="text" name="no_telp" class="form-control" value="{{ old('no_telp') }}" placeholder="08xxxxxxxxxx" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Password <span class="text-danger">*</span></label>
                <input type="password" name="password" class="form-control" placeholder="Min. 8 karakter" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password" required>
            </div>
        </div>

        <div class="section-divider"><span>Data Kependudukan</span></div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">NIK (16 digit) <span class="text-danger">*</span></label>
                <input type="text" name="nik" class="form-control" value="{{ old('nik') }}" placeholder="1234567890123456" maxlength="16" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">No. KK (16 digit) <span class="text-danger">*</span></label>
                <input type="text" name="no_kk" class="form-control" value="{{ old('no_kk') }}" placeholder="1234567890123456" maxlength="16" required>
            </div>
            <div class="col-12">
                <label class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                <textarea name="alamat" class="form-control" rows="2" placeholder="Jl. ... No. ... RT/RW ..." required>{{ old('alamat') }}</textarea>
            </div>
        </div>

        <button type="submit" class="btn-daftar">
            <i class="fas fa-paper-plane me-2"></i> Kirim Pendaftaran
        </button>
    </form>

    <div class="text-center mt-4">
        <span class="text-muted" style="font-size:.9rem;">Sudah punya akun? </span>
        <a href="{{ route('login') }}" style="color:#4f46e5;font-weight:600;text-decoration:none;">Masuk di sini</a>
        <span class="mx-2 text-muted">·</span>
        <a href="{{ route('welcome') }}" style="color:#64748b;font-size:.85rem;text-decoration:none;">← Kembali ke beranda</a>
    </div>
</div>
</body>
</html>
