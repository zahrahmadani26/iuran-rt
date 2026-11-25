<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistem Iuran RT - Dashboard Lingkungan</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3.3 & FontAwesome 6.5.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --sidebar-width: 260px;
            --navbar-height: 68px;
            --primary: #4f46e5;
            --primary-dark: #3730a3;
            --sidebar-bg: linear-gradient(180deg, #3730a3 0%, #1e1b4b 100%);
            --bg-body: #f8fafc;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-body);
            color: #1e293b;
            overflow-x: hidden;
            margin: 0;
            padding: 0;
        }

        /* Layout Structure */
        .app-layout {
            display: flex;
            min-height: 100vh;
            width: 100%;
            position: relative;
        }

        /* Sidebar */
        .app-sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: var(--sidebar-bg);
            z-index: 1040;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.08);
        }

        .app-sidebar::-webkit-scrollbar {
            width: 5px;
        }
        .app-sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
        }

        .sidebar-brand {
            padding: 24px 20px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-brand-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .sidebar-nav {
            padding: 16px 12px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .nav-section-title {
            font-size: 0.72rem;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.08em;
            color: rgba(255, 255, 255, 0.45);
            padding: 12px 14px 4px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 14px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 0.92rem;
            font-weight: 500;
            border-radius: 10px;
            transition: all 0.2s ease;
        }

        .sidebar-link i {
            width: 20px;
            font-size: 16px;
            text-align: center;
            opacity: 0.85;
            transition: transform 0.2s;
        }

        .sidebar-link:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.12);
            transform: translateX(3px);
        }

        .sidebar-link.active {
            color: #ffffff;
            background: #4f46e5;
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.4);
            font-weight: 600;
        }

        .sidebar-link.active i {
            opacity: 1;
        }

        .sidebar-submenu {
            padding-left: 32px;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .submenu-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-size: 0.85rem;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .submenu-link:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.08);
        }

        .sidebar-footer {
            padding: 14px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Main Container */
        .app-main {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1), width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Navbar */
        .app-navbar {
            height: var(--navbar-height);
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 1020;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        }

        .navbar-toggle-btn {
            background: none;
            border: 1px solid #e2e8f0;
            width: 38px;
            height: 38px;
            border-radius: 10px;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .navbar-toggle-btn:hover {
            background: #f1f5f9;
            color: #1e293b;
        }

        .user-dropdown-btn {
            background: none;
            border: 1px solid #e2e8f0;
            padding: 6px 14px 6px 10px;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #334155;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .user-dropdown-btn:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
        }

        /* Page Content */
        .app-content {
            flex: 1;
            padding: 28px;
        }

        /* Global Cards styling */
        .card {
            border: 1px solid #e2e8f0 !important;
            border-radius: 16px !important;
            background: #ffffff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02), 0 4px 12px rgba(0, 0, 0, 0.03) !important;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        /* Mobile Responsive */
        @media (max-width: 991.98px) {
            .app-sidebar {
                transform: translateX(-100%);
            }
            .app-sidebar.show {
                transform: translateX(0);
            }
            .app-main {
                margin-left: 0 !important;
                width: 100% !important;
            }
            .sidebar-backdrop {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.5);
                z-index: 1035;
                backdrop-filter: blur(2px);
            }
            .sidebar-backdrop.show {
                display: block;
            }
            .app-content {
                padding: 18px;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    @auth
    <div class="app-layout">
        <!-- Sidebar Backdrop for Mobile -->
        <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar()"></div>

        <!-- Sidebar -->
        <aside class="app-sidebar" id="appSidebar">
            <div class="sidebar-brand">
                <div class="sidebar-brand-icon">
                    <i class="fas fa-building-user"></i>
                </div>
                <div class="overflow-hidden">
                    <h6 class="text-white mb-0 fw-bold text-truncate">SI-IURAN RT</h6>
                    <small class="text-white-50 d-block text-truncate" style="font-size: 0.75rem;">Manajemen Kas Warga</small>
                </div>
            </div>

            <div class="sidebar-nav">
                <span class="nav-section-title">Menu Utama</span>

                <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-grid-horizontal"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('pengumuman.index') }}" class="sidebar-link {{ request()->routeIs('pengumuman.*') ? 'active' : '' }}">
                    <i class="fas fa-bullhorn"></i>
                    <span>Pengumuman RT</span>
                </a>
                <a href="{{ route('jadwal.index') }}" class="sidebar-link {{ request()->routeIs('jadwal.*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-days"></i>
                    <span>Jadwal Kegiatan</span>
                </a>

                <span class="nav-section-title mt-2">Layanan Warga</span>

                <a href="{{ route('pembayaran.index') }}" class="sidebar-link {{ request()->routeIs('pembayaran.*') ? 'active' : '' }}">
                    <i class="fas fa-receipt"></i>
                    <span>Pembayaran Iuran</span>
                </a>
                <a href="{{ route('surat.index') }}" class="sidebar-link {{ request()->routeIs('surat.*') ? 'active' : '' }}">
                    <i class="fas fa-file-signature"></i>
                    <span>Surat Pengantar</span>
                    @if(in_array(auth()->user()->role, ['super_admin','admin','bendahara']))
                        @php $suratPending = \App\Models\SuratPengantar::whereIn('status', ['pending','diproses'])->count(); @endphp
                        @if($suratPending > 0)
                            <span class="badge bg-info text-dark ms-auto">{{ $suratPending }}</span>
                        @endif
                    @endif
                </a>
                <a href="{{ route('laporan.index') }}" class="sidebar-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                    <i class="fas fa-comments"></i>
                    <span>Laporan & Aspirasi</span>
                </a>
                <a href="{{ route('kartu_keluarga.index') }}" class="sidebar-link {{ request()->routeIs('kartu_keluarga.*') ? 'active' : '' }}">
                    <i class="fas fa-house-user"></i>
                    <span>Data KK / Keluarga</span>
                </a>

                <span class="nav-section-title mt-2">Keamanan & Darurat</span>

                <a href="{{ route('panic.index') }}" class="sidebar-link {{ request()->routeIs('panic.*') ? 'active' : '' }}" style="{{ request()->routeIs('panic.*') ? 'background:#dc2626;' : '' }}">
                    <i class="fas fa-triangle-exclamation text-danger"></i>
                    <span>Panic Button SOS</span>
                    @php $sosAktif = \App\Models\PanicButton::where('status', 'aktif')->count(); @endphp
                    @if($sosAktif > 0)
                        <span class="badge bg-danger ms-auto">{{ $sosAktif }}</span>
                    @endif
                </a>
                <a href="{{ route('kontak_darurat.index') }}" class="sidebar-link {{ request()->routeIs('kontak_darurat.*') ? 'active' : '' }}">
                    <i class="fas fa-phone-volume"></i>
                    <span>Kontak Darurat</span>
                </a>

                @if(in_array(auth()->user()->role, ['super_admin','admin','bendahara']))
                <span class="nav-section-title mt-2">Administrasi RT</span>

                @if(auth()->user()->role === 'super_admin')
                <a href="{{ route('super_admin.index') }}" class="sidebar-link {{ request()->routeIs('super_admin.*') ? 'active' : '' }}">
                    <i class="fas fa-user-shield"></i>
                    <span>Panel Super Admin</span>
                    @php $pendingCount = \App\Models\User::where('status_akun','pending')->count(); @endphp
                    @if($pendingCount > 0)
                        <span class="badge bg-warning text-dark ms-auto">{{ $pendingCount }}</span>
                    @endif
                </a>
                @endif

                <a href="{{ route('warga.index') }}" class="sidebar-link {{ request()->routeIs('warga.*') ? 'active' : '' }}">
                    <i class="fas fa-users-rectangle"></i>
                    <span>Data Warga</span>
                </a>
                <a href="{{ route('iuran.index') }}" class="sidebar-link {{ request()->routeIs('iuran.*') ? 'active' : '' }}">
                    <i class="fas fa-hand-holding-dollar"></i>
                    <span>Tarif Iuran</span>
                </a>
                <a href="{{ route('rfid.index') }}" class="sidebar-link {{ request()->routeIs('rfid.*') ? 'active' : '' }}">
                    <i class="fas fa-id-card-clip"></i>
                    <span>RFID / IoT ESP32</span>
                </a>
                <a href="{{ route('nfc.index') }}" class="sidebar-link {{ request()->routeIs('nfc.*') ? 'active' : '' }}">
                    <i class="fas fa-mobile-screen-button"></i>
                    <span>Tap NFC Android</span>
                    <span class="badge bg-success ms-auto" style="font-size: 0.65rem;">BARU</span>
                </a>
                @endif

                @if(in_array(auth()->user()->role, ['super_admin','admin','bendahara']))
                <span class="nav-section-title mt-2">Keuangan</span>

                <a href="{{ route('keuangan.index') }}" class="sidebar-link {{ request()->routeIs('keuangan.index') ? 'active' : '' }}">
                    <i class="fas fa-chart-pie"></i>
                    <span>Laporan Kas</span>
                </a>

                <div class="sidebar-submenu">
                    <a href="{{ route('keuangan.create') }}" class="submenu-link">
                        <i class="fas fa-circle-plus text-success" style="font-size: 11px;"></i>
                        <span>+ Pemasukan</span>
                    </a>
                    <a href="{{ route('pengeluaran.create') }}" class="submenu-link">
                        <i class="fas fa-circle-minus text-danger" style="font-size: 11px;"></i>
                        <span>- Pengeluaran</span>
                    </a>
                </div>
                @endif

                <span class="nav-section-title mt-2">Pengguna</span>

                <a href="{{ route('profile.index') }}" class="sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <i class="fas fa-user-gear"></i>
                    <span>Profil Akun</span>
                </a>
            </div>

            <div class="sidebar-footer">
                <a href="#" class="sidebar-link text-danger bg-danger bg-opacity-10" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-arrow-right-from-bracket text-danger"></i>
                    <span class="text-white">Keluar Akun</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </aside>

        <!-- Main Wrapper -->
        <div class="app-main">
            <!-- Navbar -->
            <header class="app-navbar">
                <div class="d-flex align-items-center gap-3">
                    <button class="navbar-toggle-btn d-lg-none" type="button" onclick="toggleSidebar()" aria-label="Toggle menu">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div>
                        <span class="badge bg-indigo-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1">
                            <i class="fas fa-shield-halved me-1"></i>
                            @if(auth()->user()->role === 'super_admin')
                                👑 Super Admin RT
                            @else
                                {{ ucfirst(auth()->user()->role) }} RT
                            @endif
                        </span>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 gap-md-3">
                    <a href="{{ route('panic.index') }}" class="btn btn-danger btn-sm rounded-pill px-3 fw-bold d-flex align-items-center gap-1" title="Tombol Darurat SOS">
                        <i class="fas fa-triangle-exclamation"></i>
                        <span class="d-none d-sm-inline">SOS Darurat</span>
                    </a>
                    <div class="dropdown">
                        <button class="user-dropdown-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="d-none d-md-inline text-truncate" style="max-width: 150px;">{{ auth()->user()->name }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-2 py-2">
                            <li class="px-3 py-2 border-bottom">
                                <small class="text-muted d-block">Masuk sebagai:</small>
                                <span class="fw-bold text-dark">{{ auth()->user()->email }}</span>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('profile.index') }}">
                                    <i class="fas fa-user-gear me-2 text-muted"></i> Pengaturan Profil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('welcome.page') }}">
                                    <i class="fas fa-house me-2 text-muted"></i> Halaman Welcome
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <a class="dropdown-item py-2 text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-arrow-right-from-bracket me-2"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Page Content Body -->
            <main class="app-content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
                        <i class="fas fa-circle-check me-2 text-success"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
                        <i class="fas fa-circle-exclamation me-2 text-danger"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
    @endauth

    @guest
        @yield('content')
    @endguest

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('appSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar) sidebar.classList.toggle('show');
            if (backdrop) backdrop.classList.toggle('show');
        }

        // Delete confirmation helper
        document.querySelectorAll('.delete-confirm').forEach(function(element) {
            element.addEventListener('click', function(e) {
                e.preventDefault();
                const id = this.dataset.id;
                Swal.fire({
                    title: 'Hapus data ini?',
                    text: "Data yang dihapus tidak dapat dipulihkan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('delete-form-' + id);
                        if (form) form.submit();
                    }
                });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
