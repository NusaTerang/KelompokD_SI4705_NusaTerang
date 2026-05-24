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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('number', 32)->unique(); // order ID yang dikirim ke Midtrans
            $table->decimal('total_price', 12, 0);   // jumlah donasi (tanpa desimal)
            $table->string('donatur_name', 100);
            $table->string('donatur_email', 150);
            $table->string('donatur_phone', 20)->nullable();
            $table->text('pesan')->nullable();       // pesan / doa dari donatur
            $table->tinyInteger('payment_status')
                ->default(1)
                ->comment('1=menunggu pembayaran, 2=sudah dibayar, 3=kadaluarsa/expire, 4=dibatalkan');
            $table->string('snap_token')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
