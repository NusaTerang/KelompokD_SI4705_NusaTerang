<?php

namespace Database\Seeders;

use App\Models\Desa;
use App\Models\Donasi;
use App\Models\Proyek;
use App\Models\ProyekFoto;
use App\Models\User;
use Illuminate\Database\Seeder;

class RefundableProyekSeeder extends Seeder
{
    /**
     * Proyek demo yang sudah berakhir dengan sebagian dana tidak terpakai,
     * lengkap dengan donasi sukses milik Aditya Pratama — agar tombol Refund
     * langsung muncul untuk donatur tersebut.
     */
    public function run(): void
    {
        $aditya = User::where('email', 'aditya.pratama@example.com')->first();
        $desa = Desa::first();

        if (! $aditya || ! $desa) {
            return;
        }

        $proyek = Proyek::firstOrCreate(
            ['judul' => 'Surya Tanjung Lesung (Demo Refund)'],
            [
                'deskripsi' => 'Proyek PLTS yang telah selesai lebih hemat dari anggaran. Sebagian dana donasi tidak terpakai sehingga dapat direfund ke donatur.',
                'jenis_energi' => 'panel_surya',
                'target_dana' => 100000000,
                'dana_terkumpul' => 100000000,
                'dana_terpakai' => 60000000, // 40% sisa -> refundable
                'status' => 'selesai',
                'estimasi_mulai' => now()->subMonths(2)->toDateString(),
                'estimasi_selesai' => now()->subDays(2)->toDateString(), // SISA HARI sudah habis
                'created_by' => $aditya->id_donatur,
                'desa_id' => $desa->id_desa,
            ]
        );

        if ($proyek->fotos()->count() === 0) {
            ProyekFoto::create([
                'proyek_id' => $proyek->id,
                'path' => 'https://images.unsplash.com/photo-1509391366360-2e959784a276?w=800&auto=format&fit=crop&q=60',
            ]);
        }

        // Donasi Aditya yang masih bisa direfund (40% dari 500.000 = 200.000).
        Donasi::firstOrCreate(
            [
                'id_proyek'  => $proyek->id,
                'id_donatur' => $aditya->id_donatur,
                'nominal'    => 500000,
            ],
            [
                'status'        => 'success',
                'refund_status' => Donasi::REFUND_NONE,
            ]
        );
    }
}
