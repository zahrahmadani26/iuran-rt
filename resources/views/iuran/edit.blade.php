@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-6">
            <h3>Edit Iuran</h3>
        </div>
        <div class="col-6 text-end">
            <a href="{{ route('iuran.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    
    <div class="card animated-card">
        <div class="card-body">
            <form action="{{ route('iuran.update', $iuran->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label>Nama Iuran <span class="text-danger">*</span></label>
                    <input type="text" name="nama_iuran" class="form-control @error('nama_iuran') is-invalid @enderror" value="{{ old('nama_iuran', $iuran->nama_iuran) }}" required>
                    @error('nama_iuran')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label>Nominal <span class="text-danger">*</span></label>
                    <input type="number" name="nominal" class="form-control @error('nominal') is-invalid @enderror" value="{{ old('nominal', $iuran->nominal) }}" required>
                    @error('nominal')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label>Periode <span class="text-danger">*</span></label>
                    <select name="periode" class="form-control @error('periode') is-invalid @enderror" required>
                        <option value="bulanan" {{ $iuran->periode == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                        <option value="tahunan" {{ $iuran->periode == 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                    </select>
                    @error('periode')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label>Tanggal Jatuh Tempo <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_jatuh_tempo" class="form-control @error('tanggal_jatuh_tempo') is-invalid @enderror" value="{{ old('tanggal_jatuh_tempo', $iuran->tanggal_jatuh_tempo) }}" required>
                    @error('tanggal_jatuh_tempo')
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