@extends('layouts.admin')

@section('title', 'Edit Data Desa')

@section('page_heading', 'Edit Data Desa')

@section('content')
<div class="mx-auto max-w-7xl">

<form action="{{ route('desa.update', ['id' => $desa->id_desa]) }}" method="POST">
    @csrf
    @method('PUT')

    {{-- Nama Desa --}}
    <div class="mb-4">
        <label class="block text-sm font-medium">Nama Desa</label>
        <input type="text" name="nama_desa"
            value="{{ old('nama_desa', $desa->nama_desa) }}"
            class="w-full border rounded px-3 py-2">
    </div>

    {{-- Provinsi --}}
    <div class="mb-4">
        <label>Provinsi</label>
        <select name="provinsi" class="w-full border rounded px-3 py-2">
            <option value="">Pilih</option>
            <option value="Nusa Tenggara Timur" @selected(old('provinsi', $desa->provinsi) === 'Nusa Tenggara Timur')>NTT</option>
            <option value="Papua" @selected(old('provinsi', $desa->provinsi) === 'Papua')>Papua</option>
            <option value="Kalimantan Timur" @selected(old('provinsi', $desa->provinsi) === 'Kalimantan Timur')>Kaltim</option>
        </select>
    </div>

    {{-- Kabupaten --}}
    <div class="mb-4">
        <label>Kabupaten</label>
        <select name="kabupaten" class="w-full border rounded px-3 py-2">
            <option value="">Pilih</option>
            <option value="Manggarai" @selected(old('kabupaten', $desa->kabupaten) === 'Manggarai')>Manggarai</option>
            <option value="Jayawijaya" @selected(old('kabupaten', $desa->kabupaten) === 'Jayawijaya')>Jayawijaya</option>
        </select>
    </div>

    {{-- Koordinat --}}
    <div class="mb-4">
        <label>Koordinat</label>
        <input type="text" name="koordinat"
            value="{{ old('koordinat', $desa->koordinat) }}"
            class="w-full border rounded px-3 py-2">
    </div>

    {{-- Kondisi Desa --}}
    <div class="mb-4">
        <label>Kondisi Desa</label>
        <textarea name="kondisi_desa" class="w-full border rounded px-3 py-2">{{ old('kondisi_desa', $desa->kondisi_desa) }}</textarea>
    </div>

    {{-- Status Elektrifikasi (PARSE dari kondisi_desa) --}}
    @php
        $kondisi = $desa->kondisi_desa ?? '';

        $statusElektrifikasi =
            str_contains($kondisi, 'belum_teraliri') ? 'belum_teraliri' :
            (str_contains($kondisi, 'sebagian') ? 'sebagian' :
            (str_contains($kondisi, 'sudah_teraliri') ? 'sudah_teraliri' : 'sebagian'));

        $statusElektrifikasi = old('status_elektrifikasi', $statusElektrifikasi);
    @endphp

    <div class="mb-4">
        <label>Status Elektrifikasi</label>
        <select name="status_elektrifikasi" class="w-full border rounded px-3 py-2">
            <option value="belum_teraliri" @selected($statusElektrifikasi === 'belum_teraliri')>Belum Teraliri</option>
            <option value="sebagian" @selected($statusElektrifikasi === 'sebagian')>Sebagian</option>
            <option value="sudah_teraliri" @selected($statusElektrifikasi === 'sudah_teraliri')>Sudah Teraliri</option>
        </select>
    </div>

    {{-- Sumber Energi --}}
    <div class="mb-4">
        <label>Sumber Energi</label>
        <select name="sumber" class="w-full border rounded px-3 py-2">
            <option value="solar_panel" @selected(old('sumber', $desa->sumber) === 'solar_panel')>Solar</option>
            <option value="mikro_hidro" @selected(old('sumber', $desa->sumber) === 'mikro_hidro')>Mikro Hidro</option>
            <option value="biogas" @selected(old('sumber', $desa->sumber) === 'biogas')>Biogas</option>
            <option value="hybrid" @selected(old('sumber', $desa->sumber) === 'hybrid')>Hybrid</option>
        </select>
    </div>

    {{-- Status Verifikasi --}}
    <div class="mb-4">
        <label>Status Verifikasi</label>
        <select name="status_verifikasi" class="w-full border rounded px-3 py-2">
            <option value="draft" @selected(old('status_verifikasi', $desa->status_verifikasi) === 'draft')>Draft</option>
            <option value="menunggu_verifikasi" @selected(old('status_verifikasi', $desa->status_verifikasi) === 'menunggu_verifikasi')>Menunggu</option>
            <option value="terverifikasi" @selected(old('status_verifikasi', $desa->status_verifikasi) === 'terverifikasi')>Terverifikasi</option>
            <option value="ditolak" @selected(old('status_verifikasi', $desa->status_verifikasi) === 'ditolak')>Ditolak</option>
        </select>
    </div>

    {{-- Submit --}}
    <button type="submit"
        class="bg-blue-600 text-white px-4 py-2 rounded">
        Update Data
    </button>

</form>

</div>
@endsection