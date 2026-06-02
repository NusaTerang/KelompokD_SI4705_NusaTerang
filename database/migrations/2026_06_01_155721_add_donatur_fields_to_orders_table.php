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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('donatur_name')->nullable()->after('user_id');
            $table->string('donatur_email')->nullable()->after('donatur_name');
            $table->string('donatur_phone')->nullable()->after('donatur_email');
            $table->text('pesan')->nullable()->after('donatur_phone');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['donatur_name', 'donatur_email', 'donatur_phone', 'pesan']);
        });
    }
};
