<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menjalankan migration untuk tabel 'desa'.
     * Disesuaikan dengan kebutuhan Controller dan mockup Input Data Desa.
     */
    public function up(): void
    {
        Schema::create('desa', function (Blueprint $table) {
            // Primary Key menggunakan id_desa sesuai permintaan
            $table->id('id_desa');
            
            // Informasi Lokasi (Sekarang menggunakan String/Input Teks sesuai mockup baru)
            $table->string('nama_desa', 100)->index(); 
            $table->string('provinsi', 100);
            $table->string('kabupaten', 100);
            $table->string('koordinat', 100)->nullable();
            
            // Detail Kondisi Desa (Menampung gabungan data teknis dari Controller)
            $table->text('kondisi_desa')->nullable();
            
            // Pilihan Sumber Energi
            $table->enum('sumber', ['solar_panel', 'mikro_hidro', 'biogas', 'hybrid']);
            
            // Relasi ke tabel Users (Admin/Petugas yang menginput)
            $table->foreignId('id_admin')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            
            // Timestamps standar Laravel
            $table->timestamps(); 
        });
    }

    /**
     * Membatalkan migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('desa');
    }
};