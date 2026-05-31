<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Proyek;
use App\Models\ProyekFoto;
use App\Models\Desa;

class ProyekDummySeeder extends Seeder
{
    public function run(): void
    {
        $desa = Desa::first() ?? Desa::create([
            'nama_desa' => 'Desa Dummy', 'provinsi' => 'NTT', 'kabupaten' => 'Manggarai',
            'koordinat' => '0,0', 'sumber' => 'solar_panel'
        ]);

        $data = [
            [
                'judul' => 'Desa Wae Rebo Mandiri',
                'deskripsi' => 'Pemasangan PLTS Terpusat 15kWp untuk mendukung penerangan dan pendidikan 150 KK.',
                'target_dana' => 250000000,
                'dana_terkumpul' => 187500000,
                'status' => 'aktif_funding',
                'estimasi_mulai' => now(),
                'estimasi_selesai' => now()->addDays(12),
                'created_by' => 1,
                'desa_id' => $desa->id_desa,
                'image' => 'https://images.unsplash.com/photo-1508514177221-188b1cf16e9d?w=800&auto=format&fit=crop&q=60',
            ],
            [
                'judul' => 'Mikrohidro Sungai Sawai',
                'deskripsi' => 'Pemanfaatan arus sungai untuk energi berkelanjutan bagi industri pengolahan sagu lokal.',
                'target_dana' => 450000000,
                'dana_terkumpul' => 189000000,
                'status' => 'aktif_funding',
                'estimasi_mulai' => now(),
                'estimasi_selesai' => now()->addDays(24),
                'created_by' => 1,
                'desa_id' => $desa->id_desa,
                'image' => 'https://images.unsplash.com/photo-1437482078695-73f5ca6c96e2?w=800&auto=format&fit=crop&q=60',
            ],
            [
                'judul' => 'Angin Pesisir Sumba',
                'deskripsi' => 'Instalasi turbin angin skala kecil untuk mendukung pompa air bersih bagi petani pesisir.',
                'target_dana' => 120000000,
                'dana_terkumpul' => 105600000,
                'status' => 'aktif_funding',
                'estimasi_mulai' => now(),
                'estimasi_selesai' => now()->addDays(5),
                'created_by' => 1,
                'desa_id' => $desa->id_desa,
                'image' => 'https://images.unsplash.com/photo-1466611653911-95081537e5b7?w=800&auto=format&fit=crop&q=60',
            ],
            [
                'judul' => 'Terang Papua Pintar',
                'deskripsi' => 'Elektrifikasi sekolah dan pusat belajar komunitas di pedalaman Merauke menggunakan biomassa.',
                'target_dana' => 600000000,
                'dana_terkumpul' => 90000000,
                'status' => 'aktif_funding',
                'estimasi_mulai' => now(),
                'estimasi_selesai' => now()->addDays(45),
                'created_by' => 1,
                'desa_id' => $desa->id_desa,
                'image' => 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?w=800&auto=format&fit=crop&q=60',
            ],
            [
                'judul' => 'Eco-Light Wakatobi',
                'deskripsi' => 'Penerangan jalan umum ramah lingkungan untuk keamanan nelayan saat melaut di malam hari.',
                'target_dana' => 180000000,
                'dana_terkumpul' => 108000000,
                'status' => 'aktif_funding',
                'estimasi_mulai' => now(),
                'estimasi_selesai' => now()->addDays(18),
                'created_by' => 1,
                'desa_id' => $desa->id_desa,
                'image' => 'https://images.unsplash.com/photo-1498623116890-37e912163d5d?w=800&auto=format&fit=crop&q=60',
            ],
            [
                'judul' => 'Irigasi Surya Bali',
                'deskripsi' => 'Implementasi pompa air tenaga surya untuk kemandirian irigasi kelompok tani saat musim kemarau.',
                'target_dana' => 95000000,
                'dana_terkumpul' => 87400000,
                'status' => 'aktif_funding',
                'estimasi_mulai' => now(),
                'estimasi_selesai' => now()->addDays(3),
                'created_by' => 1,
                'desa_id' => $desa->id_desa,
                'image' => 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?w=800&auto=format&fit=crop&q=60',
            ]
        ];

        foreach ($data as $item) {
            $image = $item['image'];
            unset($item['image']);
            
            $proyek = Proyek::create($item);
            
            ProyekFoto::create([
                'proyek_id' => $proyek->id,
                'path' => str_replace(url('/storage') . '/', '', $image) // using the absolute url directly for dummy
            ]);
            
            // Adjust the DB to hold the absolute URL for the dummy
            \DB::table('proyek_fotos')->where('id', $proyek->fotos()->latest('id')->first()->id)->update(['path' => $image]);
        }
    }
}
