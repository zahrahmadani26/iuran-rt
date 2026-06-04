<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistem Iuran RT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideIn {
            from { transform: translateX(-100%); }
            to { transform: translateX(0); }
        }
        
        body {
            background-color: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .sidebar {
            animation: slideIn 0.5s ease-out;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 260px;
            z-index: 100;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link {
            color: white;
            transition: all 0.3s;
            border-radius: 10px;
            margin: 5px 10px;
            padding: 12px 15px;
        }
        
        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.2);
            transform: translateX(5px);
        }
        
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.3);
        }
        
        .sidebar .nav-link.text-danger:hover {
            background: rgba(255,0,0,0.2);
        }
        
        .sidebar-submenu {
            margin-left: 35px;
            font-size: 0.9rem;
        }
        
        .sidebar-submenu .nav-link {
            padding: 8px 15px;
        }
        
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: fixed;
            top: 0;
            right: 0;
            left: 260px;
            z-index: 99;
            padding: 10px 20px;
        }
        
        .main-content {
            margin-left: 260px;
            margin-top: 70px;
            padding: 20px;
            min-height: 100vh;
        }
        
        .animated-card {
            animation: fadeIn 0.6s ease-out;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .animated-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -260px;
            }
            .sidebar.show {
                margin-left: 0;
            }
            .navbar, .main-content {
                left: 0;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    @auth
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <div class="col-auto sidebar">
                <div class="text-center py-4">
                    <div class="mb-2">
                        <i class="fas fa-building fa-4x text-white"></i>
                    </div>
                    <h5 class="text-white mt-2 mb-0">Sistem Iuran RT</h5>
                    <p class="text-white-50 small mt-1">{{ auth()->user()->name }}</p>
                    <span class="badge bg-light text-dark rounded-pill px-3 py-1">{{ ucfirst(auth()->user()->role) }}</span>
                </div>
                <hr class="bg-white mx-3 my-2">
                <nav class="nav flex-column">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                    </a>
                    
                    @if(auth()->user()->role != 'warga')
                    <a href="{{ route('warga.index') }}" class="nav-link {{ request()->routeIs('warga.*') ? 'active' : '' }}">
                        <i class="fas fa-users me-2"></i> Data Warga
                    </a>
                    <a href="{{ route('iuran.index') }}" class="nav-link {{ request()->routeIs('iuran.*') ? 'active' : '' }}">
                        <i class="fas fa-money-bill-wave me-2"></i> Iuran Warga
                    </a>
                    @endif
                    
                    <a href="{{ route('pembayaran.index') }}" class="nav-link {{ request()->routeIs('pembayaran.*') ? 'active' : '' }}">
                        <i class="fas fa-credit-card me-2"></i> Pembayaran
                    </a>
                    
                    @if(auth()->user()->role != 'warga')
                    <a href="{{ route('keuangan.index') }}" class="nav-link {{ request()->routeIs('keuangan.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-line me-2"></i> Laporan Keuangan
                    </a>
                    <!-- Submenu Laporan Keuangan -->
                    <div class="sidebar-submenu">
                        <a href="{{ route('keuangan.create') }}" class="nav-link py-2">
                            <i class="fas fa-plus-circle me-2 text-success"></i> + Tambah Pemasukan
                        </a>
                        <a href="{{ route('pengeluaran.create') }}" class="nav-link py-2">
                            <i class="fas fa-minus-circle me-2 text-danger"></i> - Tambah Pengeluaran
                        </a>
                    </div>
                    @endif
                    
                    <a href="{{ route('profile.index') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                        <i class="fas fa-user me-2"></i> Profile Saya
                    </a>
                    
                    <hr class="bg-white mx-3 my-2">
                    
                    <a href="#" class="nav-link text-danger"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt me-2"></i> Keluar
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </nav>
            </div>
            
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-dark">
                <div class="container-fluid">
                    <button class="btn btn-link text-white d-md-none p-0" type="button" onclick="toggleSidebar()">
                        <i class="fas fa-bars fa-lg"></i>
                    </button>
                    <div class="ms-auto d-flex align-items-center">
                        <div class="dropdown">
                            <button class="btn btn-link text-white dropdown-toggle p-0" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle fa-lg me-1"></i>
                                {{ auth()->user()->name }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('profile.index') }}">
                                    <i class="fas fa-user me-2"></i> Profile
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
            
            <!-- Main Content -->
            <main class="main-content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show animated-card" role="alert">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show animated-card" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
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
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('show');
        }
        
        // Auto close alert
        setTimeout(function() {
            document.querySelectorAll('.alert').forEach(function(alert) {
                setTimeout(function() {
                    alert.style.opacity = '0';
                    alert.style.transition = 'opacity 0.5s';
                    setTimeout(function() {
                        if(alert) alert.remove();
                    }, 500);
                }, 3000);
            });
        }, 100);
        
        // Delete confirmation
        document.querySelectorAll('.delete-confirm').forEach(function(element) {
            element.addEventListener('click', function(e) {
                e.preventDefault();
                const id = this.dataset.id;
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('delete-form-' + id).submit();
                    }
                });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>