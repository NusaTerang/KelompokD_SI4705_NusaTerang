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
            $table->unsignedBigInteger('id_proyek')->nullable();
            $table->string('tipe', 30);
            $table->decimal('nominal', 15, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('id_donatur')
                ->references('id_donatur')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign('id_proyek')
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
