@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm rounded-4 border-0">
                <div class="card-header bg-white border-0 pt-4">
                    @php
                        $isPengeluaran = request()->routeIs('pengeluaran.create') || (isset($jenis) && $jenis == 'pengeluaran');
                        $title = $isPengeluaran ? 'Tambah Pengeluaran' : 'Tambah Pemasukan';
                        $icon = $isPengeluaran ? 'fa-minus-circle text-danger' : 'fa-plus-circle text-success';
                        $btnClass = $isPengeluaran ? 'btn-danger' : 'btn-success';
                        $jenisValue = $isPengeluaran ? 'pengeluaran' : 'pemasukan';
                    @endphp
                    <h4 class="mb-0">
                        <i class="fas {{ $icon }} me-2"></i> 
                        {{ $title }}
                    </h4>
                    <p class="text-muted small mt-2">
                        {{ $isPengeluaran ? 'Catat pengeluaran kas RT' : 'Catat pemasukan kas RT' }}
                    </p>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('keuangan.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <input type="hidden" name="jenis" value="{{ $jenisValue }}">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-tag me-1 {{ $isPengeluaran ? 'text-danger' : 'text-success' }}"></i> 
                                Kategori
                            </label>
                            <select name="kategori" class="form-select @error('kategori') is-invalid @enderror" required>
                                <option value="">Pilih Kategori</option>
                                @if($isPengeluaran)
                                    <option value="Kegiatan Rutin">🎉 Kegiatan Rutin</option>
                                    <option value="Kebersihan">🧹 Kebersihan</option>
                                    <option value="Perawatan">🔧 Perawatan</option>
                                    <option value="Listrik">💡 Listrik</option>
                                    <option value="Air">💧 Air</option>
                                    <option value="Keamanan">👮 Keamanan</option>
                                    <option value="Sosial">🤝 Sosial</option>
                                    <option value="Lainnya">📌 Lainnya</option>
                                @else
                                    <option value="Iuran Warga">💰 Iuran Warga</option>
                                    <option value="Donasi">🤲 Donasi</option>
                                    <option value="Kas RT">🏦 Kas RT</option>
                                    <option value="Lainnya">📌 Lainnya</option>
                                @endif
                            </select>
                            @error('kategori')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-file-alt me-1 {{ $isPengeluaran ? 'text-danger' : 'text-success' }}"></i> 
                                Deskripsi
                            </label>
                            <input type="text" name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" 
                                   placeholder="{{ $isPengeluaran ? 'Contoh: Beli alat kebersihan, Rapat RT' : 'Contoh: Pembayaran iuran, Donasi' }}" required>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-money-bill-wave me-1 {{ $isPengeluaran ? 'text-danger' : 'text-success' }}"></i> 
                                Nominal
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">Rp</span>
                                <input type="number" name="nominal" class="form-control @error('nominal') is-invalid @enderror" 
                                       placeholder="0" min="0" step="1000" required>
                            </div>
                            <small class="text-muted">Masukkan nominal dalam Rupiah</small>
                            @error('nominal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-calendar-alt me-1 {{ $isPengeluaran ? 'text-danger' : 'text-success' }}"></i> 
                                Tanggal
                            </label>
                            <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" 
                                   value="{{ date('Y-m-d') }}" required>
                            @error('tanggal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-paperclip me-1 {{ $isPengeluaran ? 'text-danger' : 'text-success' }}"></i> 
                                Bukti (Opsional)
                            </label>
                            <input type="file" name="bukti" class="form-control @error('bukti') is-invalid @enderror" 
                                   accept="image/*,application/pdf">
                            <small class="text-muted">Upload bukti/foto (JPG, PNG, PDF, max 2MB)</small>
                            @error('bukti')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="d-flex justify-content-between gap-2">
                            <a href="{{ route('keuangan.index') }}" class="btn btn-outline-secondary px-4">
                                <i class="fas fa-arrow-left me-2"></i> Batal
                            </a>
                            <button type="submit" class="btn {{ $btnClass }} px-4">
                                <i class="fas fa-save me-2"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Informasi Saldo -->
            <div class="card mt-3 bg-light border-0 rounded-4">
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <small class="text-muted">Saldo Kas Saat Ini</small>
                            <h5 class="mb-0 {{ ($saldoKas ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                Rp {{ number_format($saldoKas ?? 0, 0, ',', '.') }}
                            </h5>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-info-circle text-muted" data-bs-toggle="tooltip" title="Saldo = Total Pemasukan - Total Pengeluaran"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Tooltip
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endsection