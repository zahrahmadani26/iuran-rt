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
        <div class="d-flex align-items-center justify-content-between gap-2">
            <a href="{{ route('welcome') }}" class="nav-brand">
                <div class="brand-icon">
                    <i class="fas fa-building-user"></i>
                </div>
                SI-IURAN RT
            </a>
            <div class="d-none d-md-flex align-items-center gap-4">
                <a href="#fitur" class="text-decoration-none fw-semibold text-secondary">Fitur Web</a>
                <a href="#peran" class="text-decoration-none fw-semibold text-secondary">Menu &amp; Hak Akses</a>
                <a href="#cara-daftar" class="text-decoration-none fw-semibold text-secondary">Cara Daftar</a>
            </div>
            <div class="d-flex gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary rounded-pill px-4" style="font-weight:600;">
                        <i class="fas fa-gauge me-1"></i> Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-primary rounded-pill px-4" style="font-weight:600;">
                        <i class="fas fa-sign-in-alt me-1"></i> Masuk
                    </a>
                    <a href="{{ route('register.warga') }}" class="btn btn-primary rounded-pill px-4" style="font-weight:600;">
                        <i class="fas fa-user-plus me-1"></i> Daftar
                    </a>
                @endauth
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
                    Portal Digital &amp; Smart Kas RT Terpadu
                </div>
                <h1 class="hero-title">
                    Kelola RT &amp; Iuran<br>Lebih <span>Mudah &amp; Transparan</span>
                </h1>
                <p class="hero-desc">
                    Satu aplikasi untuk seluruh kebutuhan warga dan pengurus RT: bayar iuran tap RFID/NFC, pengajuan surat pengantar PDF, pengumuman, agenda kegiatan, laporan aspirasi, data KK, hingga tombol darurat SOS.
                </p>
                <div class="hero-cta">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-hero-primary">
                            <i class="fas fa-gauge me-2"></i>Masuk ke Dashboard
                        </a>
                    @else
                        <a href="{{ route('register.warga') }}" class="btn-hero-primary">
                            <i class="fas fa-user-plus me-2"></i>Daftar Sebagai Warga
                        </a>
                        <a href="{{ route('login') }}" class="btn-hero-outline">
                            <i class="fas fa-sign-in-alt me-2"></i>Sudah Punya Akun
                        </a>
                    @endauth
                    <a href="#fitur" class="btn-hero-outline">
                        <i class="fas fa-list-check me-2"></i>Lihat Semua Fitur
                    </a>
                </div>
            </div>
            <div class="col-lg-6 hero-visual">
                <div class="hero-card">
                    <div class="mb-3 d-flex align-items-center gap-2">
                        <i class="fas fa-cubes" style="color:#fbbf24;font-size:1.2rem;"></i>
                        <span style="font-weight:700;">12 Modul Utama di Dalam Web</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-4 hero-stat">
                            <div class="hero-stat-num">💳</div>
                            <div class="hero-stat-num" style="font-size:1.5rem;">RFID/NFC</div>
                            <div class="hero-stat-label">Tap Bayar Cepat</div>
                        </div>
                        <div class="col-4 hero-stat">
                            <div class="hero-stat-num">📄</div>
                            <div class="hero-stat-num" style="font-size:1.5rem;">Surat PDF</div>
                            <div class="hero-stat-label">Pengantar Online</div>
                        </div>
                        <div class="col-4 hero-stat">
                            <div class="hero-stat-num">🚨</div>
                            <div class="hero-stat-num" style="font-size:1.5rem;">SOS</div>
                            <div class="hero-stat-label">Panic Button</div>
                        </div>
                    </div>
                    <hr style="border-color:rgba(255,255,255,0.2);margin:16px 0;">
                    <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:rgba(255,255,255,0.1);">
                        <i class="fas fa-shield-check" style="color:#4ade80;font-size:1.3rem;"></i>
                        <div>
                            <div style="font-weight:700;font-size:.95rem;">Terverifikasi Super Admin</div>
                            <div style="opacity:.75;font-size:.8rem;">Warga daftar mandiri, akun diaktifkan oleh Ketua RT</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features -->
<section class="features-section" id="fitur">
    <div class="container">
        <h2 class="section-title">Semua Fitur yang Terdapat di Web</h2>
        <p class="section-sub">Jelajahi lengkapnya layanan digital untuk warga dan pengurus RT sebelum masuk</p>
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon" style="background:#ede9fe;color:#7c3aed;">
                        <i class="fas fa-id-card-clip"></i>
                    </div>
                    <div class="feature-title">1. Tap Kartu RFID (ESP32)</div>
                    <p class="feature-desc">Bayar iuran otomatis cukup tempel kartu RFID ke mesin pembaca RC522 berbasis ESP32. Riwayat tap tercatat langsung ke kas RT.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon" style="background:#dcfce7;color:#16a34a;">
                        <i class="fas fa-mobile-screen-button"></i>
                    </div>
                    <div class="feature-title">2. Tap NFC via HP Android</div>
                    <p class="feature-desc">Alternatif tanpa alat tambahan: pengurus bisa menempelkan kartu langsung ke sensor NFC HP Android lewat browser Chrome.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon" style="background:#e0e7ff;color:#4f46e5;">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="feature-title">3. Dashboard &amp; Grafik Keuangan</div>
                    <p class="feature-desc">Pantau saldo kas RT, grafik pemasukan vs pengeluaran bulanan, rasio kepatuhan bayar warga, dan kategori pengeluaran secara real-time.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon" style="background:#fef3c7;color:#d97706;">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    <div class="feature-title">4. Surat Pengantar Digital (PDF)</div>
                    <p class="feature-desc">Ajukan surat domisili, pengantar KTP, SKCK, usaha, atau SKTM online. Setelah disetujui RT, nomor surat otomatis terbit dan siap unduh PDF.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon" style="background:#fee2e2;color:#dc2626;">
                        <i class="fas fa-triangle-exclamation"></i>
                    </div>
                    <div class="feature-title">5. Panic Button SOS Darurat</div>
                    <p class="feature-desc">Tombol darurat 1-klik untuk kebakaran, kemalingan, kecelakaan, atau kondisi medis yang mengirim alert aktif ke dashboard pengurus RT.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon" style="background:#ffe4e6;color:#e11d48;">
                        <i class="fas fa-phone-volume"></i>
                    </div>
                    <div class="feature-title">6. Kontak Darurat 24 Jam</div>
                    <p class="feature-desc">Direktori nomor penting Ketua RT, Satpam, Polisi (110), Pemadam Kebakaran (113), Ambulans (119), dan PLN (123) dengan tombol telepon langsung.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon" style="background:#e0f2fe;color:#0284c7;">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <div class="feature-title">7. Papan Pengumuman RT</div>
                    <p class="feature-desc">Pengumuman resmi berkategori Umum, Penting, hingga Darurat dengan fitur sematkan (pin) di atas dan tampil langsung di beranda dashboard.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon" style="background:#ecfeff;color:#0891b2;">
                        <i class="fas fa-calendar-days"></i>
                    </div>
                    <div class="feature-title">8. Jadwal Kegiatan Lingkungan</div>
                    <p class="feature-desc">Kalender kegiatan warga seperti kerja bakti, rapat RT, posyandu, arisan, dan olahraga lengkap dengan jam dan lokasi acara.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon" style="background:#fce7f3;color:#db2777;">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="feature-title">9. Laporan &amp; Aspirasi Warga</div>
                    <p class="feature-desc">Laporkan masalah fasilitas lingkungan (lampu mati, jalan rusak, sampah) lengkap dengan lokasi &amp; foto bukti, serta tanggapan dari pengurus.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon" style="background:#f0fdf4;color:#15803d;">
                        <i class="fas fa-house-user"></i>
                    </div>
                    <div class="feature-title">10. Data Kartu Keluarga (KK)</div>
                    <p class="feature-desc">Pendataan lengkap kepala keluarga dan anggota keluarga per rumah (NIK, hubungan, TTL, pekerjaan, status kawin) dalam satu halaman terpadu.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon" style="background:#f3e8ff;color:#9333ea;">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="feature-title">11. Panel Super Admin &amp; Verifikasi</div>
                    <p class="feature-desc">Setiap warga yang mendaftar harus diverifikasi Ketua RT (Super Admin) sebelum akun aktif. Pengurus juga dapat mengatur role Admin &amp; Bendahara.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon" style="background:#f1f5f9;color:#334155;">
                        <i class="fas fa-file-excel"></i>
                    </div>
                    <div class="feature-title">12. Tarif Iuran &amp; Export Kas Excel</div>
                    <p class="feature-desc">Atur tarif iuran bulanan, verifikasi bukti transfer manual maupun QR/RFID, catat kas masuk/keluar, dan unduh laporan keuangan ke Excel.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Ringkasan Hak Akses & Menu -->
<section class="py-5" id="peran" style="background:#f8fafc;border-top:1px solid #e2e8f0;border-bottom:1px solid #e2e8f0;">
    <div class="container py-4">
        <h2 class="section-title">Menu Berdasarkan Peran Pengguna</h2>
        <p class="section-sub">Setiap akun mendapat tampilan menu yang sesuai kebutuhannya</p>
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="feature-icon mb-0" style="background:#e0e7ff;color:#4f46e5;">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">Akun Warga</h5>
                            <small class="text-muted">Menu yang bisa diakses setelah mendaftar &amp; diverifikasi</small>
                        </div>
                    </div>
                    <ul class="list-unstyled mb-0 d-grid gap-2 text-secondary">
                        <li><i class="fas fa-check-circle text-success me-2"></i><strong>Dashboard Portal Warga:</strong> Ringkasan kas RT, pengumuman, agenda &amp; grafik</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i><strong>Pembayaran Iuran:</strong> Bayar &amp; pantau riwayat pembayaran iuran sendiri</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i><strong>Surat Pengantar Digital:</strong> Ajukan surat &amp; download PDF setelah disetujui</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i><strong>Laporan &amp; Aspirasi:</strong> Kirim keluhan/saran beserta foto &amp; pantau progres</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i><strong>Data KK / Keluarga:</strong> Kelola anggota keluarga pada Kartu Keluarga sendiri</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i><strong>Pengumuman &amp; Jadwal:</strong> Baca info resmi dan agenda lingkungan RT</li>
                        <li><i class="fas fa-check-circle text-danger me-2"></i><strong>Panic Button SOS &amp; Kontak Darurat:</strong> Kirim sinyal bahaya &amp; telepon darurat</li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="feature-icon mb-0" style="background:#f3e8ff;color:#7c3aed;">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">Super Admin / Admin / Bendahara RT</h5>
                            <small class="text-muted">Seluruh fitur warga ditambah kontrol penuh manajemen RT</small>
                        </div>
                    </div>
                    <ul class="list-unstyled mb-0 d-grid gap-2 text-secondary">
                        <li><i class="fas fa-shield-halved text-primary me-2"></i><strong>Panel Super Admin:</strong> Setujui/tolak pendaftar warga baru &amp; atur role pengurus</li>
                        <li><i class="fas fa-shield-halved text-primary me-2"></i><strong>Data Warga &amp; KK:</strong> Kelola seluruh warga, kartu RFID, dan data semua KK</li>
                        <li><i class="fas fa-shield-halved text-primary me-2"></i><strong>RFID ESP32 &amp; Tap NFC Android:</strong> Proses pembayaran tap kartu langsung</li>
                        <li><i class="fas fa-shield-halved text-primary me-2"></i><strong>Persetujuan Surat &amp; Laporan:</strong> Terbitkan nomor surat PDF &amp; tanggapi aduan</li>
                        <li><i class="fas fa-shield-halved text-primary me-2"></i><strong>Keuangan &amp; Tarif Iuran:</strong> Catat kas masuk/keluar, verifikasi bayar, export Excel</li>
                        <li><i class="fas fa-shield-halved text-primary me-2"></i><strong>Kelola Pengumuman &amp; Jadwal:</strong> Publikasikan pengumuman pin &amp; agenda RT</li>
                        <li><i class="fas fa-shield-halved text-danger me-2"></i><strong>Penanganan SOS &amp; Kontak Darurat:</strong> Tangani alert SOS &amp; kelola nomor penting</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How it works -->
<section class="how-section" id="cara-daftar">
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
