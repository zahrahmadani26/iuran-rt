@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card animated-card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="avatar-circle" style="width: 100px; height: 100px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                            <i class="fas fa-user fa-4x text-white"></i>
                        </div>
                    </div>
                    <h4>{{ auth()->user()->name }}</h4>
                    <p class="text-muted">
                        <span class="badge bg-primary">{{ ucfirst(auth()->user()->role) }}</span>
                    </p>
                    <p class="text-muted">
                        <i class="fas fa-envelope"></i> {{ auth()->user()->email }}
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-md-8 mb-4">
            <div class="card animated-card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Edit Profile</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label>Nama Lengkap</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', auth()->user()->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', auth()->user()->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label>Password Baru (Kosongkan jika tidak ingin mengubah)</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label>Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Profile
                        </button>
                    </form>
                </div>
            </div>
            
            @if(auth()->user()->role == 'warga' && auth()->user()->warga)
            <div class="card animated-card mt-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Informasi Warga</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="150">NIK</th>
                            <td>: {{ auth()->user()->warga->nik }}</td>
                        </tr>
                        <tr>
                            <th>No KK</th>
                            <td>: {{ auth()->user()->warga->no_kk }}</td>
                        </tr>
                        <tr>
                            <th>Alamat</th>
                            <td>: {{ auth()->user()->warga->alamat }}</td>
                        </tr>
                        <tr>
                            <th>No Telp</th>
                            <td>: {{ auth()->user()->warga->no_telp }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>: <span class="badge bg-{{ auth()->user()->warga->status == 'aktif' ? 'success' : 'danger' }}">{{ auth()->user()->warga->status }}</span></td>
                        </tr>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection