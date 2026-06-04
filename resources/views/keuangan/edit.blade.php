@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-6">
            <h3>Edit Transaksi Keuangan</h3>
        </div>
        <div class="col-6 text-end">
            <a href="{{ route('keuangan.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    
    <div class="card animated-card">
        <div class="card-body">
            <form action="{{ route('keuangan.update', $keuangan->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label>Jenis Transaksi <span class="text-danger">*</span></label>
                    <select name="jenis" class="form-control @error('jenis') is-invalid @enderror" required>
                        <option value="pemasukan" {{ $keuangan->jenis == 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                        <option value="pengeluaran" {{ $keuangan->jenis == 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                    </select>
                    @error('jenis')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label>Kategori <span class="text-danger">*</span></label>
                    <select name="kategori" class="form-control @error('kategori') is-invalid @enderror" required>
                        <option value="Iuran Warga" {{ $keuangan->kategori == 'Iuran Warga' ? 'selected' : '' }}>Iuran Warga</option>
                        <option value="Kegiatan Rutin" {{ $keuangan->kategori == 'Kegiatan Rutin' ? 'selected' : '' }}>Kegiatan Rutin</option>
                        <option value="Kebersihan" {{ $keuangan->kategori == 'Kebersihan' ? 'selected' : '' }}>Kebersihan</option>
                        <option value="Perawatan" {{ $keuangan->kategori == 'Perawatan' ? 'selected' : '' }}>Perawatan</option>
                        <option value="Lainnya" {{ $keuangan->kategori == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('kategori')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label>Deskripsi <span class="text-danger">*</span></label>
                    <input type="text" name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" value="{{ old('deskripsi', $keuangan->deskripsi) }}" required>
                    @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label>Nominal <span class="text-danger">*</span></label>
                    <input type="number" name="nominal" class="form-control @error('nominal') is-invalid @enderror" value="{{ old('nominal', $keuangan->nominal) }}" required>
                    @error('nominal')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label>Tanggal <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', $keuangan->tanggal) }}" required>
                    @error('tanggal')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update
                </button>
            </form>
        </div>
    </div>
</div>
@endsection