<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang — SI-IURAN RT</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #3730a3;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f0f4ff;
            min-height: 100vh;
            margin: 0;
        }

        /* ---- NAVBAR ---- */
        .welcome-nav {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 14px 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 8px rgba(0,0,0,.06);
        }
        .nav-brand {
            font-weight: 800;
            font-size: 1.25rem;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .nav-brand .brand-icon {
            width: 38px; height: 38px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 1rem;
        }

        /* ---- HERO ---- */
        .hero-section {
            background: linear-gradient(135deg, #3730a3 0%, #4f46e5 50%, #6366f1 100%);
            color: white;
            padding: 90px 0 70px;
            position: relative;
            overflow: hidden;
        }
        .hero-section::before {
            content: '';
            position: absolute;
            top: -60px; right: -60px;
            width: 350px; height: 350px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }
        .hero-section::after {
            content: '';
            position: absolute;
            bottom: -80px; left: -40px;
            width: 280px; height: 280px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 999px;
            padding: 6px 16px;
            font-size: 0.85rem;
            margin-bottom: 24px;
        }
        .hero-title {
            font-size: 3rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 20px;
        }
        .hero-title span { color: #fbbf24; }
        .hero-desc {
            font-size: 1.1rem;
            opacity: 0.85;
            max-width: 540px;
            line-height: 1.7;
        }
        .hero-cta {
            margin-top: 36px;
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
        }
        .btn-hero-primary {
            background: white;
            color: var(--primary);
            font-weight: 700;
            padding: 14px 32px;
            border-radius: 12px;
            border: none;
            font-size: 1rem;
            text-decoration: none;
            transition: all .2s;
            box-shadow: 0 4px 20px rgba(0,0,0,.15);
        }
        .btn-hero-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(0,0,0,.2);
            color: var(--primary-dark);
        }
        .btn-hero-outline {
            background: transparent;
            color: white;
            font-weight: 600;
            padding: 14px 28px;
            border-radius: 12px;
            border: 2px solid rgba(255,255,255,0.5);
            font-size: 1rem;
            text-decoration: none;
            transition: all .2s;
        }
        .btn-hero-outline:hover {
            background: rgba(255,255,255,0.1);
            border-color: white;
            color: white;
        }
        .hero-visual {
            position: relative;
            z-index: 1;
        }
        .hero-card {
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 20px;
            padding: 28px;
            color: white;
        }
        .hero-stat {
            text-align: center;
            padding: 16px;
        }
        .hero-stat-num {
            font-size: 2.2rem;
            font-weight: 800;
            color: #fbbf24;
        }
        .hero-stat-label {
            font-size: 0.8rem;
            opacity: 0.8;
        }

        /* ---- FEATURES ---- */
        .features-section { padding: 80px 0; background: white; }
        .section-title {
            font-size: 2rem;
            font-weight: 800;
            color: #1e293b;
            text-align: center;
            margin-bottom: 12px;
        }
        .section-sub {
            color: #64748b;
            text-align: center;
            margin-bottom: 50px;
            font-size: 1.05rem;
        }
        .feature-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 32px 28px;
            height: 100%;
            transition: all .2s;
        }
        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(79,70,229,0.1);
            border-color: var(--primary);
        }
        .feature-icon {
            width: 56px; height: 56px;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 20px;
        }
        .feature-title {
            font-weight: 700;
            font-size: 1.1rem;
            color: #1e293b;
            margin-bottom: 10px;
        }
        .feature-desc { color: #64748b; font-size: 0.95rem; line-height: 1.6; }

        /* ---- HOW IT WORKS ---- */
        .how-section { padding: 80px 0; background: #f0f4ff; }
        .step-card {
            text-align: center;
            padding: 20px;
        }
        .step-num {
            width: 56px; height: 56px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem;
            font-weight: 800;
            margin: 0 auto 16px;
        }

        /* ---- CTA SECTION ---- */
        .cta-section {
            padding: 80px 0;
            background: linear-gradient(135deg, #1e1b4b 0%, #3730a3 100%);
            color: white;
            text-align: center;
        }

        /* ---- FOOTER ---- */
        .welcome-footer {
            background: #1e293b;
            color: #94a3b8;
            padding: 30px 0;
            text-align: center;
            font-size: 0.9rem;
        }

        @media(max-width: 768px) {
            .hero-title { font-size: 2rem; }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="welcome-nav">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between">
            <a href="{{ route('welcome') }}" class="nav-brand">
                <div class="brand-icon">
                    <i class="fas fa-building-user"></i>
                </div>
                SI-IURAN RT
            </a>
            <div class="d-flex gap-2">
                <a href="{{ route('login') }}" class="btn btn-outline-primary rounded-pill px-4" style="font-weight:600;">
                    <i class="fas fa-sign-in-alt me-1"></i> Masuk
                </a>
                <a href="{{ route('register.warga') }}" class="btn btn-primary rounded-pill px-4" style="font-weight:600;">
                    <i class="fas fa-user-plus me-1"></i> Daftar
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- Hero -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6" style="position:relative;z-index:1;">
                <div class="hero-badge">
                    <i class="fas fa-star" style="color:#fbbf24;"></i>
                    Sistem Digital Iuran RT Terpercaya
                </div>
                <h1 class="hero-title">
                    Kelola Iuran RT<br>Lebih <span>Mudah & Transparan</span>
                </h1>
                <p class="hero-desc">
                    Platform digital untuk warga RT. Bayar iuran cukup dengan tap kartu RFID atau NFC Android. 
                    Semua tercatat otomatis, laporan kas real-time.
                </p>
                <div class="hero-cta">
                    <a href="{{ route('register.warga') }}" class="btn-hero-primary">
                        <i class="fas fa-user-plus me-2"></i>Daftar Sebagai Warga
                    </a>
                    <a href="{{ route('login') }}" class="btn-hero-outline">
                        <i class="fas fa-sign-in-alt me-2"></i>Sudah Punya Akun
                    </a>
                </div>
            </div>
            <div class="col-lg-6 hero-visual">
                <div class="hero-card">
                    <div class="mb-3 d-flex align-items-center gap-2">
                        <i class="fas fa-chart-line" style="color:#fbbf24;font-size:1.2rem;"></i>
                        <span style="font-weight:700;">Statistik RT</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-4 hero-stat">
                            <div class="hero-stat-num">🏠</div>
                            <div class="hero-stat-num" style="font-size:1.8rem;">RT</div>
                            <div class="hero-stat-label">Digital</div>
                        </div>
                        <div class="col-4 hero-stat">
                            <div class="hero-stat-num">📲</div>
                            <div class="hero-stat-num" style="font-size:1.8rem;">NFC</div>
                            <div class="hero-stat-label">Tap Bayar</div>
                        </div>
                        <div class="col-4 hero-stat">
                            <div class="hero-stat-num">📊</div>
                            <div class="hero-stat-num" style="font-size:1.8rem;">Real</div>
                            <div class="hero-stat-label">Time Report</div>
                        </div>
                    </div>
                    <hr style="border-color:rgba(255,255,255,0.2);margin:16px 0;">
                    <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:rgba(255,255,255,0.1);">
                        <i class="fas fa-shield-check" style="color:#4ade80;font-size:1.3rem;"></i>
                        <div>
                            <div style="font-weight:700;font-size:.95rem;">Terverifikasi Admin</div>
                            <div style="opacity:.7;font-size:.8rem;">Akun warga diverifikasi langsung oleh Ketua RT</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features -->
<section class="features-section">
    <div class="container">
        <h2 class="section-title">Fitur Unggulan</h2>
        <p class="section-sub">Semua yang kamu butuhkan untuk kelola RT digital ada di sini</p>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon" style="background:#ede9fe;color:#7c3aed;">
                        <i class="fas fa-id-card-clip"></i>
                    </div>
                    <div class="feature-title">Tap RFID / NFC</div>
                    <p class="feature-desc">Bayar iuran hanya dengan menempelkan kartu RFID ke mesin atau tap NFC lewat HP Android. Langsung tercatat otomatis.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon" style="background:#dcfce7;color:#16a34a;">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <div class="feature-title">Laporan Kas Transparan</div>
                    <p class="feature-desc">Semua pemasukan dan pengeluaran kas RT tercatat real-time. Warga bisa pantau kondisi keuangan RT kapan saja.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon" style="background:#fef3c7;color:#d97706;">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="feature-title">Verifikasi Super Admin</div>
                    <p class="feature-desc">Setiap pendaftaran warga baru harus diverifikasi oleh Ketua RT sebagai Super Admin sebelum dapat mengakses sistem.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon" style="background:#fce7f3;color:#db2777;">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div class="feature-title">Riwayat Pembayaran</div>
                    <p class="feature-desc">Setiap warga dapat melihat riwayat pembayaran iurannya sendiri. Bukti bayar tersedia kapan saja.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon" style="background:#e0f2fe;color:#0284c7;">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div class="feature-title">Notifikasi Real-time</div>
                    <p class="feature-desc">Admin mendapat notifikasi langsung setiap ada warga baru mendaftar atau ada pembayaran masuk.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon" style="background:#f0fdf4;color:#15803d;">
                        <i class="fas fa-mobile-screen-button"></i>
                    </div>
                    <div class="feature-title">Ramah Mobile</div>
                    <p class="feature-desc">Tampilan responsif, nyaman diakses dari HP Android maupun laptop. Tidak perlu install aplikasi tambahan.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How it works -->
<section class="how-section">
    <div class="container">
        <h2 class="section-title">Cara Daftar</h2>
        <p class="section-sub">Hanya 3 langkah mudah untuk bergabung</p>
        <div class="row g-4 justify-content-center">
            <div class="col-md-4">
                <div class="step-card">
                    <div class="step-num">1</div>
                    <h5 class="fw-700" style="font-weight:700;color:#1e293b;">Isi Formulir Pendaftaran</h5>
                    <p class="text-muted">Lengkapi data diri seperti NIK, nama, alamat, dan nomor telepon</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="step-card">
                    <div class="step-num">2</div>
                    <h5 style="font-weight:700;color:#1e293b;">Tunggu Verifikasi Admin</h5>
                    <p class="text-muted">Ketua RT akan memverifikasi data kamu. Proses biasanya 1×24 jam</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="step-card">
                    <div class="step-num">3</div>
                    <h5 style="font-weight:700;color:#1e293b;">Akun Aktif, Siap Bayar!</h5>
                    <p class="text-muted">Setelah diverifikasi, login dan nikmati kemudahan bayar iuran digital</p>
                </div>
            </div>
        </div>
        <div class="text-center mt-5">
            <a href="{{ route('register.warga') }}" class="btn btn-primary btn-lg rounded-pill px-5 py-3" style="font-weight:700;font-size:1.1rem;">
                <i class="fas fa-user-plus me-2"></i> Daftar Sekarang — Gratis!
            </a>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container">
        <h2 style="font-size:2rem;font-weight:800;margin-bottom:12px;">Sudah punya akun?</h2>
        <p style="opacity:.8;font-size:1.05rem;margin-bottom:32px;">Masuk sekarang dan kelola iuran RT dengan mudah</p>
        <a href="{{ route('login') }}" class="btn btn-light btn-lg rounded-pill px-5 py-3" style="font-weight:700;color:var(--primary);font-size:1.05rem;">
            <i class="fas fa-sign-in-alt me-2"></i> Masuk ke Dashboard
        </a>
    </div>
</section>

<!-- Footer -->
<footer class="welcome-footer">
    <div class="container">
        <p class="mb-1">
            <i class="fas fa-building-user me-2" style="color:#4f46e5;"></i>
            <strong style="color:white;">SI-IURAN RT</strong> — Sistem Informasi Iuran Rumah Tangga
        </p>
        <p class="mb-0" style="font-size:.8rem;">
            Dibuat dengan <i class="fas fa-heart text-danger"></i> untuk kemajuan RT digital Indonesia
        </p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
