@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="container-fluid p-0">

    <div class="mb-4">
        <h4 class="fw-bold mb-1"><i class="fas fa-user-circle text-primary me-2"></i>Profil Saya</h4>
        <p class="text-muted mb-0 small">Kelola informasi akun Anda</p>
    </div>

    @include('layouts.alerts')

    <div class="row g-4">
        {{-- Profile Card --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 text-center">
                <div class="card-body p-4">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle text-white fw-bold"
                         style="width:88px;height:88px;font-size:2rem;background:linear-gradient(135deg,#4f46e5,#7c3aed);">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <h5 class="fw-bold mb-1">{{ auth()->user()->name }}</h5>
                    <span class="badge bg-primary rounded-pill px-3 py-2 mb-2">{{ ucfirst(auth()->user()->role) }}</span>
                    <p class="text-muted small mb-0">
                        <i class="fas fa-envelope me-1"></i> {{ auth()->user()->email }}
                    </p>
                    @if(auth()->user()->warga)
                    <hr class="my-3">
                    <div class="text-start small">
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">NIK</span>
                            <code>{{ auth()->user()->warga->nik }}</code>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">No KK</span>
                            <code>{{ auth()->user()->warga->no_kk }}</code>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">No Telp</span>
                            <span>{{ auth()->user()->warga->no_telp }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">Status</span>
                            <span class="badge bg-{{ auth()->user()->warga->status == 'aktif' ? 'success' : 'danger' }}-subtle text-{{ auth()->user()->warga->status == 'aktif' ? 'success' : 'danger' }} rounded-pill">
                                {{ ucfirst(auth()->user()->warga->status) }}
                            </span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Edit Form --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h6 class="fw-bold mb-0"><i class="fas fa-pen me-2 text-primary"></i>Edit Informasi Akun</h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name"
                                   class="form-control rounded-3 @error('name') is-invalid @enderror"
                                   value="{{ old('name', auth()->user()->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email"
                                   class="form-control rounded-3 @error('email') is-invalid @enderror"
                                   value="{{ old('email', auth()->user()->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <hr class="my-3">
                        <p class="text-muted small mb-3"><i class="fas fa-lock me-1"></i> Ganti Password (kosongkan jika tidak ingin mengubah)</p>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Password Baru</label>
                            <input type="password" name="password"
                                   class="form-control rounded-3 @error('password') is-invalid @enderror"
                                   placeholder="Min. 8 karakter">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation"
                                   class="form-control rounded-3" placeholder="Ulangi password baru">
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4 rounded-pill">
                                <i class="fas fa-save me-2"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @if(auth()->user()->warga)
            <div class="card border-0 shadow-sm rounded-4 mt-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h6 class="fw-bold mb-0"><i class="fas fa-id-card me-2 text-primary"></i>Informasi Warga</h6>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="row g-2">
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted d-block">Alamat</small>
                                <span class="fw-semibold small">{{ auth()->user()->warga->alamat }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
