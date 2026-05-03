<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Skema mengikuti ERD (MySQL): id_desa, nama_desa, provinsi, kabupaten, koordinat,
 * kondisi_desa, sumber (enum sumber_desa), status_verifikasi (enum status_verifikasi),
 * id_admin, created_at.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('desa', function (Blueprint $table) {
            $table->id('id_desa');
            $table->string('nama_desa', 100);
            $table->string('provinsi', 100);
            $table->string('kabupaten', 100);
            $table->string('koordinat', 100)->nullable();
            $table->text('kondisi_desa')->nullable();
            $table->enum('sumber', ['solar_panel', 'mikro_hidro', 'biogas', 'hybrid']);
            $table->enum('status_verifikasi', ['draft', 'menunggu_verifikasi', 'terverifikasi', 'ditolak']);
            $table->foreignId('id_admin')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('desa');
    }
};
