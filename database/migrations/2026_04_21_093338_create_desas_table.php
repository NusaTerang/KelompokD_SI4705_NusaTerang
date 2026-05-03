<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('desas', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('provinsi', 100);
            $table->string('kabupaten', 100);
            $table->decimal('lat', 10, 6)->nullable();
            $table->decimal('lng', 10, 6)->nullable();
            $table->enum('jenis_energi', ['solar', 'mikro_hidro'])->default('solar');
            $table->bigInteger('estimasi_biaya')->default(0);
            $table->enum('status', ['terverifikasi', 'pending'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('desas');
    }
};
