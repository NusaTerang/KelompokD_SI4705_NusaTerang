@extends('layouts.profile')

@section('title', 'Detail Donasi')

@section('content')

    <div class="max-w-3xl mx-auto py-10 px-4">

        <div class="bg-white rounded-2xl shadow-lg border border-slate-200 p-8">

            {{-- Header --}}
            <div class="flex items-center gap-3 mb-8">

                <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center">
                    <span class="material-symbols-outlined text-yellow-700">
                        description
                    </span>
                </div>

                <h1 class="text-3xl font-bold text-[#0D3B73]">
                    Detail Transaksi Donasi
                </h1>

            </div>

            {{-- Data --}}
            <div class="divide-y divide-slate-200">

                <div class="flex items-center justify-between py-4">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-blue-500">tag</span>
                        <span class="text-slate-500">ID Donasi</span>
                    </div>

                    <span class="font-bold">
                        #{{ $donasi->id_donasi }}
                    </span>
                </div>

                <div class="flex items-center justify-between py-4">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-blue-500">description</span>
                        <span class="text-slate-500">Proyek</span>
                    </div>

                    <span class="font-semibold">
                        {{ $donasi->proyek->judul }}
                    </span>
                </div>

                <div class="flex items-center justify-between py-4">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-blue-500">payments</span>
                        <span class="text-slate-500">Nominal</span>
                    </div>

                    <span class="text-xl font-bold">
                        Rp {{ number_format($donasi->nominal,0,',','.') }}
                    </span>
                </div>

                <div class="flex items-center justify-between py-4">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-green-500">check_circle</span>
                        <span class="text-slate-500">Status</span>
                    </div>

                    @if($donasi->status == 'success')
                        <span class="font-bold text-green-600">
                            SUKSES
                        </span>
                    @elseif($donasi->status == 'pending')
                        <span class="font-bold text-yellow-600">
                            PENDING
                        </span>
                    @else
                        <span class="font-bold text-red-600">
                            {{ strtoupper($donasi->status) }}
                        </span>
                    @endif

                </div>

                <div class="flex items-center justify-between py-4">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-blue-500">calendar_month</span>
                        <span class="text-slate-500">Tanggal Donasi</span>
                    </div>

                    <span class="font-semibold">
                        {{ $donasi->created_at->format('d F Y H:i') }}
                    </span>
                </div>

                <div class="flex items-center justify-between py-4">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-blue-500">sync</span>
                        <span class="text-slate-500">Status Refund</span>
                    </div>

                    @if($donasi->refund_status == 'none')
                        <span>None</span>
                    @elseif($donasi->refund_status == 'refunded')
                        <span class="text-blue-600 font-semibold">
                            Sudah Refund
                        </span>
                    @elseif($donasi->refund_status == 'ikhlas')
                        <span class="text-purple-600 font-semibold">
                            Direlakan
                        </span>
                    @endif

                </div>

            </div>

            {{-- Info --}}
            <div class="mt-6 rounded-xl border border-blue-100 bg-blue-50 p-4 flex gap-3">

                <span class="material-symbols-outlined text-blue-600">
                    info
                </span>

                <div>

                    <p class="font-semibold text-blue-800">
                        Terima kasih atas kontribusi Anda!
                    </p>

                    <p class="text-sm text-blue-700">
                        Donasi Anda sangat berarti dalam mendukung penyediaan energi
                        terbarukan untuk desa-desa di Indonesia.
                    </p>

                </div>

            </div>

            {{-- Button --}}
            <div class="mt-8 border-t pt-6">

                <a href="{{ route('profil.edit') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-yellow-400 px-6 py-3 font-semibold hover:bg-yellow-500 transition">

                    <span class="material-symbols-outlined text-base">
                        arrow_back
                    </span>

                    Kembali

                </a>

            </div>

        </div>

    </div>

@endsection