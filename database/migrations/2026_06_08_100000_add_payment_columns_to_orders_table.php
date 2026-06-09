<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('payment_status');
            $table->decimal('amount_saldo', 15, 2)->default(0.00)->after('payment_method');
            $table->decimal('amount_qris', 15, 2)->default(0.00)->after('amount_saldo');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'amount_saldo', 'amount_qris']);
        });
    }
};
