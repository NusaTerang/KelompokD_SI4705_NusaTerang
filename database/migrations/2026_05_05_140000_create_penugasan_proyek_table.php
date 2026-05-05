<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penugasan_proyek', function (Blueprint $table) {
            $table->bigIncrements('id_penugasan');
            $table->unsignedBigInteger('id_proyek');
            $table->unsignedBigInteger('id_penyedia');
            $table->enum('status_penugasan', ['pending', 'diterima', 'ditolak'])->default('pending');
            $table->timestamp('tanggal_respon')->nullable();
            $table->timestamps();

            $table->foreign('id_proyek')->references('id')->on('proyeks')->cascadeOnDelete();
            $table->foreign('id_penyedia')->references('id')->on('penyedia_energis')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penugasan_proyek');
    }
};
