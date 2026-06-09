<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saldo_mutasi', function (Blueprint $table) {
            $table->id('id_mutasi');
            $table->foreignId('id_donatur')->constrained('users', 'id_donatur')->cascadeOnDelete();
            // refund | donasi | topup | penyesuaian
            $table->string('tipe');
            // Bertanda: positif = saldo masuk, negatif = saldo keluar
            $table->decimal('nominal', 15, 2);
            $table->decimal('saldo_sebelum', 15, 2);
            $table->decimal('saldo_sesudah', 15, 2);
            $table->string('keterangan')->nullable();
            $table->foreignId('proyek_id')->nullable()->constrained('proyeks')->nullOnDelete();
            $table->timestamps();

            $table->index('id_donatur');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saldo_mutasi');
    }
};
