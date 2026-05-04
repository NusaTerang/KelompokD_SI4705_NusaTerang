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
        Schema::table('penyedia_energis', function (Blueprint $table) {
            $table->string('kota', 100)->nullable()->after('provinsi_operasi');
            $table->decimal('latitude', 10, 7)->nullable()->after('kota');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->decimal('kapasitas_maks', 10, 2)->nullable()->after('longitude');
            $table->text('deskripsi')->nullable()->after('kapasitas_maks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penyedia_energis', function (Blueprint $table) {
            $table->dropColumn(['kota', 'latitude', 'longitude', 'kapasitas_maks', 'deskripsi']);
        });
    }
};
