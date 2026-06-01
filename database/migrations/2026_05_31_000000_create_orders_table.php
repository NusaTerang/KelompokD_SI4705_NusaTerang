<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('number', 32)->unique();
            $table->decimal('total_price', 12, 0);
            $table->string('donatur_name', 100);
            $table->string('donatur_email', 150);
            $table->string('donatur_phone', 20)->nullable();
            $table->text('pesan')->nullable();
            $table->tinyInteger('payment_status')
                ->default(1)
                ->comment('1=menunggu, 2=berhasil, 3=expired, 4=dibatalkan');
            $table->string('snap_token')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
