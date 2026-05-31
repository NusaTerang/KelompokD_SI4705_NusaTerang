<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proyeks', function (Blueprint $table) {
            $table->string('status', 50)->default('draft')->change();
            $table->boolean('expired_extension_pending')->default(false)->after('dana_terkumpul');
            $table->date('expired_original_end_date')->nullable()->after('expired_extension_pending');
            $table->timestamp('expired_extended_at')->nullable()->after('expired_original_end_date');
            $table->string('expired_vendor_decision', 20)->nullable()->after('expired_extended_at');
        });
    }

    public function down(): void
    {
        Schema::table('proyeks', function (Blueprint $table) {
            $table->dropColumn([
                'expired_extension_pending',
                'expired_original_end_date',
                'expired_extended_at',
                'expired_vendor_decision',
            ]);
        });
    }
};
