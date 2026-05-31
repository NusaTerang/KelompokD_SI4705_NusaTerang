<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('donasi')) {
            return;
        }

        Schema::create('donasi', function (Blueprint $table) {
            $table->id('id_donasi');
            $table->unsignedBigInteger('id_donatur');
            $table->unsignedBigInteger('id_proyek');
            $table->decimal('nominal', 15, 2)->default(0);
            $table->string('status')->default('berhasil');
            $table->timestamps();

            $table->foreign('id_donatur')->references('id_donatur')->on('users')->cascadeOnDelete();
            $table->foreign('id_proyek')->references('id')->on('proyeks')->cascadeOnDelete();
            $table->index(['id_proyek', 'id_donatur']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donasi');
    }
};
