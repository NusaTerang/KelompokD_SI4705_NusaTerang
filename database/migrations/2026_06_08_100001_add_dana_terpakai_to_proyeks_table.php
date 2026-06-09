<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proyeks', function (Blueprint $table) {
            // Dana yang benar-benar terpakai saat proyek berakhir (diisi admin).
            // Sisa (dana_terkumpul - dana_terpakai) menjadi dasar refund donatur.
            $table->decimal('dana_terpakai', 15, 2)->default(0)->after('dana_terkumpul');
        });
    }

    public function down(): void
    {
        Schema::table('proyeks', function (Blueprint $table) {
            $table->dropColumn('dana_terpakai');
        });
    }
};
