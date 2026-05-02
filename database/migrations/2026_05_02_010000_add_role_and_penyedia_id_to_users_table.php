<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'penyedia', 'user'])->default('user')->after('password');
            $table->unsignedBigInteger('penyedia_id')->nullable()->after('role');
            $table->foreign('penyedia_id')->references('id')->on('penyedia_energis')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['penyedia_id']);
            $table->dropColumn(['role', 'penyedia_id']);
        });
    }
};
