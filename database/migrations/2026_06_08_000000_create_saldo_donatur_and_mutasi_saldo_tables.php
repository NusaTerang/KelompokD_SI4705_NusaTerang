<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('saldo_donatur')) {
            Schema::create('saldo_donatur', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_donatur')->unique();
                $table->decimal('saldo', 15, 2)->default(0.00);
                $table->timestamps();

                $table->foreign('id_donatur')->references('id_donatur')->on('users')->cascadeOnDelete();
            });
        }

        if (!Schema::hasTable('mutasi_saldo')) {
            Schema::create('mutasi_saldo', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_donatur');
                $table->decimal('nominal', 15, 2);
                $table->enum('tipe', ['masuk', 'keluar']);
                $table->string('keterangan')->nullable();
                $table->timestamps();

                $table->foreign('id_donatur')->references('id_donatur')->on('users')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('mutasi_saldo');
        Schema::dropIfExists('saldo_donatur');
    }
};
