@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-0 pt-4">
                    <h4 class="mb-0">
                        <i class="fas fa-credit-card text-primary me-2"></i> 
                        Form Pembayaran Iuran
                    </h4>
                    <p class="text-muted small mt-2">Isi form dibawah ini untuk melakukan pembayaran iuran</p>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('pembayaran.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        @if(auth()->user()->role != 'warga')
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pilih Warga</label>
                            <select name="warga_id" class="form-select @error('warga_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Warga --</option>
                                @foreach($wargas as $warga)
                                    <option value="{{ $warga->id }}" {{ old('warga_id') == $warga->id ? 'selected' : '' }}>
                                        {{ $warga->nama_lengkap }} ({{ $warga->nik }})
                                    </option>
                                @endforeach
                            </select>
                            @error('warga_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @else
                            @php
                                $warga = Auth::user()->warga;
                            @endphp
                            <input type="hidden" name="warga_id" value="{{ $warga->id ?? '' }}">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Membayar sebagai: <strong>{{ $warga->nama_lengkap ?? Auth::user()->name }}</strong>
                            </div>
                        @endif
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pilih Iuran</label>
                            <select name="iuran_id" class="form-select @error('iuran_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Jenis Iuran --</option>
                                @foreach($iurans as $iuran)
                                    <option value="{{ $iuran->id }}" {{ old('iuran_id') == $iuran->id ? 'selected' : '' }}>
                                        {{ $iuran->nama_iuran }} - Rp {{ number_format($iuran->nominal, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                            @error('iuran_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Jumlah Dibayar</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="jumlah_dibayar" class="form-control @error('jumlah_dibayar') is-invalid @enderror" 
                                       placeholder="Masukkan nominal" value="{{ old('jumlah_dibayar') }}" required>
                            </div>
                            @error('jumlah_dibayar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tanggal Bayar</label>
                            <input type="date" name="tanggal_bayar" class="form-control @error('tanggal_bayar') is-invalid @enderror" 
                                   value="{{ old('tanggal_bayar', date('Y-m-d')) }}" required>
                            @error('tanggal_bayar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Bukti Transfer</label>
                            <input type="file" name="bukti_transfer" class="form-control @error('bukti_transfer') is-invalid @enderror" 
                                   accept="image/*">
                            <small class="text-muted">Upload foto/screenshot bukti transfer (JPG, PNG, max 2MB)</small>
                            @error('bukti_transfer')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between gap-2">
                            <a href="{{ route('pembayaran.index') }}" class="btn btn-outline-secondary px-4">
                                <i class="fas fa-arrow-left me-2"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i> Simpan Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection