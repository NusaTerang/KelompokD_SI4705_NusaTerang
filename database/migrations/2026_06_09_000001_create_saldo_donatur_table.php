<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saldo_donatur', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_donatur')->unique();
            $table->decimal('saldo', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('id_donatur')
                  ->references('id_donatur')
                  ->on('users')
                  ->cascadeOnDelete();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('saldo_donatur');
    }
};
