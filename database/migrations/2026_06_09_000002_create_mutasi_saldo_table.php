<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mutasi_saldo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_donatur');
            $table->enum('tipe', ['refund', 'donasi', 'topup', 'masuk', 'keluar']);
            $table->decimal('nominal', 15, 2);
            $table->decimal('saldo_sebelum', 15, 2)->nullable();
            $table->decimal('saldo_sesudah', 15, 2)->nullable();
            $table->unsignedBigInteger('referensi_proyek_id')->nullable();
            $table->string('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('id_donatur')
                  ->references('id_donatur')
                  ->on('users')
                  ->cascadeOnDelete();

            $table->foreign('referensi_proyek_id')
                  ->references('id')
                  ->on('proyeks')
                  ->nullOnDelete();

            $table->index(['id_donatur', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mutasi_saldo');
    }
};
