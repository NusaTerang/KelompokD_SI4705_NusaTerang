<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penyedia_energis', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('email')->unique()->nullable();
            $table->string('no_telepon', 20)->nullable();
            $table->enum('spesialisasi', ['solar', 'mikro_hidro', 'lainnya']);
            $table->string('provinsi_operasi', 100)->nullable();
            $table->bigInteger('kisaran_harga_min')->nullable();
            $table->bigInteger('kisaran_harga_max')->nullable();
            $table->decimal('rating', 3, 1)->default(0);
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penyedia_energis');
    }
};
