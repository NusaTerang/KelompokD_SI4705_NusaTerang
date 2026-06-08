<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('topups', function (Blueprint $table) {
            $table->id();
            $table->string('number', 50)->unique();
            $table->foreignId('user_id')->constrained('users', 'id_donatur')->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->tinyInteger('payment_status')->default(1)->comment('1=pending, 2=success, 3=expired, 4=cancelled');
            $table->string('snap_token', 36)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('topups');
    }
};
