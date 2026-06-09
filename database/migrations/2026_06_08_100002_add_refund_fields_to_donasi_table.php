<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donasi', function (Blueprint $table) {
            // none = belum diproses, refunded = dikembalikan ke saldo, ikhlas = direlakan
            $table->string('refund_status')->default('none')->after('status');
            $table->decimal('refund_amount', 15, 2)->nullable()->after('refund_status');
            $table->timestamp('refunded_at')->nullable()->after('refund_amount');
        });
    }

    public function down(): void
    {
        Schema::table('donasi', function (Blueprint $table) {
            $table->dropColumn(['refund_status', 'refund_amount', 'refunded_at']);
        });
    }
};
