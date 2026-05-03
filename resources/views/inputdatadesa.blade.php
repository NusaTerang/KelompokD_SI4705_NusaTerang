@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Input Data Desa</h2>

    {{-- Notifikasi sukses --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Validasi error --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Terjadi kesalahan!</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('desa.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Nama Desa</label>
            <input type="text" name="nama_desa" class="form-control" value="{{ old('nama_desa') }}">
        </div>

        <div class="mb-3">
            <label>Kecamatan</label>
            <input type="text" name="kecamatan" class="form-control" value="{{ old('kecamatan') }}">
        </div>

        <div class="mb-3">
            <label>Kabupaten</label>
            <input type="text" name="kabupaten" class="form-control" value="{{ old('kabupaten') }}">
        </div>

        <div class="mb-3">
            <label>Kode Pos</label>
            <input type="text" name="kode_pos" class="form-control" value="{{ old('kode_pos') }}">
        </div>

        <div class="mb-3">
            <label>Jumlah Penduduk</label>
            <input type="number" name="jumlah_penduduk" class="form-control" value="{{ old('jumlah_penduduk') }}">
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('desa.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection