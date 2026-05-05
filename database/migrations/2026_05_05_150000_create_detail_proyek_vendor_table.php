<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_proyek_vendor', function (Blueprint $table) {
            $table->bigIncrements('id_detail');
            $table->unsignedBigInteger('id_penugasan');
            $table->json('jenis_energi')->nullable();
            $table->decimal('kapasitas_daya', 10, 2)->nullable();
            $table->string('satuan_daya', 10)->default('kWp');
            $table->decimal('target_dana', 15, 2)->nullable();
            $table->json('cost_breakdown')->nullable();
            $table->unsignedSmallInteger('durasi_minggu')->nullable();
            $table->text('catatan_teknis')->nullable();
            $table->enum('status', ['draft', 'submitted'])->default('draft');
            // Legacy fields kept for compatibility
            $table->text('spesifikasi')->nullable();
            $table->decimal('estimasi_biaya', 15, 2)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('id_penugasan')
                  ->references('id_penugasan')
                  ->on('penugasan_proyek')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_proyek_vendor');
    }
};
