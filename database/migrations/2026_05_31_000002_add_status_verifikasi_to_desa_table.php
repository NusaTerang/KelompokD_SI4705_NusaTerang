<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('desa', function (Blueprint $table) {
            $table->string('status_verifikasi')->default('belum_terverifikasi')->after('sumber');
        });
    }

    public function down(): void
    {
        Schema::table('desa', function (Blueprint $table) {
            $table->dropColumn('status_verifikasi');
        });
    }
};
