@extends('layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="fas fa-user-shield text-primary me-2"></i>Panel Super Admin</h4>
        <p class="text-muted mb-0">Kelola verifikasi warga dan manajemen pengguna sistem</p>
    </div>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 rounded-4 shadow-sm h-100" style="background:linear-gradient(135deg,#fef3c7,#fde68a);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:48px;height:48px;background:#f59e0b;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:white;">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <div style="font-size:1.8rem;font-weight:800;color:#92400e;">{{ $stats['pending'] }}</div>
                        <div style="font-size:.8rem;color:#78350f;">Menunggu Verifikasi</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 rounded-4 shadow-sm h-100" style="background:linear-gradient(135deg,#dcfce7,#bbf7d0);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:48px;height:48px;background:#16a34a;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:white;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <div style="font-size:1.8rem;font-weight:800;color:#14532d;">{{ $stats['aktif'] }}</div>
                        <div style="font-size:.8rem;color:#166534;">Warga Aktif</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 rounded-4 shadow-sm h-100" style="background:linear-gradient(135deg,#fee2e2,#fecaca);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:48px;height:48px;background:#dc2626;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:white;">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div>
                        <div style="font-size:1.8rem;font-weight:800;color:#7f1d1d;">{{ $stats['ditolak'] }}</div>
                        <div style="font-size:.8rem;color:#991b1b;">Ditolak</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 rounded-4 shadow-sm h-100" style="background:linear-gradient(135deg,#ede9fe,#ddd6fe);">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:48px;height:48px;background:#7c3aed;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:white;">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div>
                        <div style="font-size:1.8rem;font-weight:800;color:#3b0764;">{{ $stats['admin'] }}</div>
                        <div style="font-size:.8rem;color:#4c1d95;">Admin / Bendahara</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- PENDING VERIFICATION --}}
@if($pendingUsers->count() > 0)
<div class="card border-0 rounded-4 shadow-sm mb-4">
    <div class="card-header bg-warning bg-opacity-10 border-0 rounded-top-4 py-3 px-4">
        <h6 class="mb-0 fw-bold text-warning">
            <i class="fas fa-clock me-2"></i>
            Menunggu Verifikasi 
            <span class="badge bg-warning text-dark ms-2">{{ $pendingUsers->count() }}</span>
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>NIK</th>
                        <th>Alamat</th>
                        <th>No. Telp</th>
                        <th>Daftar</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingUsers as $i => $user)
                    <tr>
                        <td class="ps-4 text-muted">{{ $i+1 }}</td>
                        <td class="fw-semibold">{{ $user->name }}</td>
                        <td><small class="text-muted">{{ $user->email }}</small></td>
                        <td><code>{{ $user->warga->nik ?? '-' }}</code></td>
                        <td><small>{{ Str::limit($user->warga->alamat ?? '-', 40) }}</small></td>
                        <td><small>{{ $user->warga->no_telp ?? '-' }}</small></td>
                        <td><small class="text-muted">{{ $user->created_at->diffForHumans() }}</small></td>
                        <td class="text-center pe-4">
                            <div class="d-flex gap-1 justify-content-center">
                                <form action="{{ route('super_admin.verifikasi', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm rounded-3" onclick="return confirm('Setujui akun {{ $user->name }}?')">
                                        <i class="fas fa-check me-1"></i>Setujui
                                    </button>
                                </form>
                                <form action="{{ route('super_admin.tolak', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm rounded-3" onclick="return confirm('Tolak akun {{ $user->name }}?')">
                                        <i class="fas fa-times me-1"></i>Tolak
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@else
<div class="alert border-0 rounded-4 mb-4" style="background:#f0fdf4;color:#166534;">
    <i class="fas fa-check-circle me-2"></i> Tidak ada pendaftaran yang menunggu verifikasi.
</div>
@endif

{{-- SEMUA WARGA AKTIF / DITOLAK --}}
<div class="card border-0 rounded-4 shadow-sm mb-4">
    <div class="card-header bg-white border-0 rounded-top-4 py-3 px-4 d-flex align-items-center justify-content-between">
        <h6 class="mb-0 fw-bold"><i class="fas fa-users me-2 text-primary"></i>Semua Warga Terdaftar</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Status Akun</th>
                        <th>Role</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allWargaUsers as $i => $user)
                    <tr>
                        <td class="ps-4 text-muted">{{ $i+1 }}</td>
                        <td class="fw-semibold">{{ $user->name }}</td>
                        <td><small class="text-muted">{{ $user->email }}</small></td>
                        <td>
                            @if($user->status_akun === 'aktif')
                                <span class="badge bg-success bg-opacity-15 text-success border border-success border-opacity-25 rounded-pill px-3">
                                    <i class="fas fa-circle me-1" style="font-size:.5rem;"></i>Aktif
                                </span>
                            @elseif($user->status_akun === 'ditolak')
                                <span class="badge bg-danger bg-opacity-15 text-danger border border-danger border-opacity-25 rounded-pill px-3">
                                    <i class="fas fa-circle me-1" style="font-size:.5rem;"></i>Ditolak
                                </span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('super_admin.ubah_role', $user->id) }}" method="POST" class="d-flex gap-1">
                                @csrf
                                @method('PATCH')
                                <select name="role" class="form-select form-select-sm rounded-3" style="width:130px;">
                                    <option value="warga" {{ $user->role == 'warga' ? 'selected' : '' }}>Warga</option>
                                    <option value="bendahara" {{ $user->role == 'bendahara' ? 'selected' : '' }}>Bendahara</option>
                                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-outline-primary rounded-3">
                                    <i class="fas fa-save"></i>
                                </button>
                            </form>
                        </td>
                        <td class="text-center pe-4">
                            <form action="{{ route('super_admin.hapus', $user->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-3" onclick="return confirm('Hapus akun {{ $user->name }}? Tindakan ini tidak bisa dibatalkan.')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Belum ada warga terdaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ADMIN & BENDAHARA --}}
<div class="card border-0 rounded-4 shadow-sm">
    <div class="card-header bg-white border-0 rounded-top-4 py-3 px-4">
        <h6 class="mb-0 fw-bold"><i class="fas fa-user-tie me-2 text-purple"></i>Admin & Bendahara</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allAdmins as $i => $user)
                    <tr>
                        <td class="ps-4 text-muted">{{ $i+1 }}</td>
                        <td class="fw-semibold">
                            {{ $user->name }}
                            @if($user->id === auth()->id())
                                <span class="badge bg-primary bg-opacity-10 text-primary ms-1" style="font-size:.7rem;">Kamu</span>
                            @endif
                        </td>
                        <td><small class="text-muted">{{ $user->email }}</small></td>
                        <td>
                            @php
                                $roleColors = ['super_admin'=>'purple','admin'=>'primary','bendahara'=>'info'];
                                $roleColor = $roleColors[$user->role] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $roleColor }} bg-opacity-10 text-{{ $roleColor }} border border-{{ $roleColor }} border-opacity-25 rounded-pill px-3">
                                {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                            </span>
                        </td>
                        <td><span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Aktif</span></td>
                        <td class="text-center pe-4">
                            @if($user->id !== auth()->id())
                            <form action="{{ route('super_admin.hapus', $user->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-3" onclick="return confirm('Hapus akun {{ $user->name }}?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @else
                            <span class="text-muted" style="font-size:.8rem;">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Tidak ada data.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
