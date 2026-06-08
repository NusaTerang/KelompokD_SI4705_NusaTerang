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
        Schema::create('donasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_proyek')->constrained('proyeks')->cascadeOnDelete();
            $table->foreignId('id_donatur')->constrained('users', 'id_donatur')->cascadeOnDelete();
            $table->decimal('nominal', 15, 2);
            $table->enum('status', ['success', 'pending', 'failed', 'refunded'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donasi');
    }
};
