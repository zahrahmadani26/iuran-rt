@extends('layouts.app')

@section('title', 'Edit Iuran')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex align-items-center mb-4 gap-3">
        <a href="{{ route('iuran.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
        <div>
            <h4 class="fw-bold mb-0"><i class="fas fa-edit text-primary me-2"></i>Edit Iuran</h4>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form action="{{ route('iuran.update', $iuran->id) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Iuran <span class="text-danger">*</span></label>
                            <input type="text" name="nama_iuran"
                                   class="form-control rounded-3 @error('nama_iuran') is-invalid @enderror"
                                   value="{{ old('nama_iuran', $iuran->nama_iuran) }}" required>
                            @error('nama_iuran')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nominal <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">Rp</span>
                                <input type="number" name="nominal"
                                       class="form-control rounded-end-3 @error('nominal') is-invalid @enderror"
                                       value="{{ old('nominal', $iuran->nominal) }}" min="0" step="1000" required>
                            </div>
                            @error('nominal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Periode <span class="text-danger">*</span></label>
                            <select name="periode" class="form-select rounded-3 @error('periode') is-invalid @enderror" required>
                                <option value="bulanan" {{ $iuran->periode == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                                <option value="tahunan" {{ $iuran->periode == 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                            </select>
                            @error('periode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Tanggal Jatuh Tempo <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_jatuh_tempo"
                                   class="form-control rounded-3 @error('tanggal_jatuh_tempo') is-invalid @enderror"
                                   value="{{ old('tanggal_jatuh_tempo', $iuran->tanggal_jatuh_tempo) }}" required>
                            @error('tanggal_jatuh_tempo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <hr class="my-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4 rounded-pill">
                                <i class="fas fa-save me-2"></i> Update
                            </button>
                            <a href="{{ route('iuran.index') }}" class="btn btn-outline-secondary px-4 rounded-pill">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
