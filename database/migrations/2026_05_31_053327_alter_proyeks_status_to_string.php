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
        Schema::table('proyeks', function (Blueprint $table) {
            $table->string('status', 50)->default('draft')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proyeks', function (Blueprint $table) {
            $table->enum('status', [
                'draft',
                'menunggu_konfirmasi_penyedia',
                'diterima_penyedia',
                'menunggu_review_admin',
                'aktif_funding',
                'eksekusi',
                'selesai',
                'ditolak'
            ])->default('draft')->change();
        });
    }
};
