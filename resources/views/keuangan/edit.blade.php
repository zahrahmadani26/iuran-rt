@extends('layouts.app')

@section('title', 'Edit Transaksi')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex align-items-center mb-4 gap-3">
        <a href="{{ route('keuangan.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
        <div>
            <h4 class="fw-bold mb-0"><i class="fas fa-edit text-primary me-2"></i>Edit Transaksi</h4>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form action="{{ route('keuangan.update', $keuangan->id) }}" method="POST">
                        @csrf @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jenis Transaksi <span class="text-danger">*</span></label>
                            <select name="jenis" class="form-select rounded-3 @error('jenis') is-invalid @enderror" required>
                                <option value="pemasukan"  {{ $keuangan->jenis=='pemasukan'  ? 'selected':'' }}>Pemasukan</option>
                                <option value="pengeluaran"{{ $keuangan->jenis=='pengeluaran'? 'selected':'' }}>Pengeluaran</option>
                            </select>
                            @error('jenis')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                            <select name="kategori" class="form-select rounded-3 @error('kategori') is-invalid @enderror" required>
                                @foreach(['Iuran Warga','Donasi','Kas RT','Kegiatan Rutin','Kebersihan','Perawatan','Listrik','Air','Keamanan','Sosial','Lainnya'] as $kat)
                                    <option value="{{ $kat }}" {{ $keuangan->kategori==$kat ? 'selected':'' }}>{{ $kat }}</option>
                                @endforeach
                            </select>
                            @error('kategori')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Deskripsi <span class="text-danger">*</span></label>
                            <input type="text" name="deskripsi"
                                   class="form-control rounded-3 @error('deskripsi') is-invalid @enderror"
                                   value="{{ old('deskripsi', $keuangan->deskripsi) }}" required>
                            @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nominal <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">Rp</span>
                                <input type="number" name="nominal"
                                       class="form-control rounded-end-3 @error('nominal') is-invalid @enderror"
                                       value="{{ old('nominal', $keuangan->nominal) }}" min="0" step="1000" required>
                            </div>
                            @error('nominal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal"
                                   class="form-control rounded-3 @error('tanggal') is-invalid @enderror"
                                   value="{{ old('tanggal', $keuangan->tanggal) }}" required>
                            @error('tanggal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <hr class="my-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4 rounded-pill">
                                <i class="fas fa-save me-2"></i> Update
                            </button>
                            <a href="{{ route('keuangan.index') }}" class="btn btn-outline-secondary px-4 rounded-pill">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
