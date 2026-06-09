<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_akhir_proyek_vendor', function (Blueprint $table) {
            $table->id('id_laporan');
            $table->foreignId('id_penugasan')->constrained('penugasan_proyek', 'id_penugasan')->cascadeOnDelete();
            $table->foreignId('id_proyek')->constrained('proyeks')->cascadeOnDelete();
            $table->foreignId('id_penyedia')->constrained('penyedia_energis')->cascadeOnDelete();
            $table->text('deskripsi')->nullable();
            $table->decimal('kapasitas_terpasang', 12, 2)->nullable();
            $table->string('satuan_kapasitas', 10)->default('kWp');
            $table->json('foto_paths')->nullable();
            $table->text('catatan')->nullable();
            $table->enum('status', ['draft', 'submitted'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->unique(['id_penugasan', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_akhir_proyek_vendor');
    }
};
