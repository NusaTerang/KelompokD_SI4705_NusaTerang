<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('desas', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('provinsi');
            $table->string('kabupaten');
            $table->double('lat');
            $table->double('lng');
            $table->enum('jenis_energi', ['solar', 'mikro_hidro', 'lainnya']);
            $table->bigInteger('estimasi_biaya');
            $table->enum('status', ['terverifikasi', 'belum_terverifikasi'])->default('terverifikasi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('desas');
    }
};
