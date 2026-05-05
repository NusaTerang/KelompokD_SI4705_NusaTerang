<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('donatur')) {
            return;
        }

        // Drop FKs referencing donatur
        if (Schema::hasTable('desa')) {
            Schema::table('desa', function (Blueprint $table) {
                $table->dropForeign(['id_admin']);
            });
        }

        if (Schema::hasTable('donasi')) {
            Schema::table('donasi', function (Blueprint $table) {
                $table->dropForeign(['id_donatur']);
            });
        }

        // Drop stale users table if it exists (from default Laravel migration)
        if (Schema::hasTable('users')) {
            if (Schema::hasColumn('users', 'penyedia_id')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropForeign(['penyedia_id']);
                });
            }
            Schema::drop('users');
        }

        // Rename donatur -> users
        Schema::rename('donatur', 'users');

        // Recreate FKs referencing users.id_donatur
        if (Schema::hasTable('desa')) {
            Schema::table('desa', function (Blueprint $table) {
                $table->foreign('id_admin')->references('id_donatur')->on('users')->nullOnDelete();
            });
        }

        if (Schema::hasTable('donasi')) {
            Schema::table('donasi', function (Blueprint $table) {
                $table->foreign('id_donatur')->references('id_donatur')->on('users')->cascadeOnDelete();
            });
        }

        // Restore proyeks.created_by FK (dropped by previous migration, never restored)
        if (Schema::hasTable('proyeks') && Schema::hasColumn('proyeks', 'created_by')) {
            Schema::table('proyeks', function (Blueprint $table) {
                $table->foreign('created_by')->references('id_donatur')->on('users')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        // Drop FKs referencing users
        if (Schema::hasTable('desa')) {
            Schema::table('desa', function (Blueprint $table) {
                $table->dropForeign(['id_admin']);
            });
        }

        if (Schema::hasTable('donasi')) {
            Schema::table('donasi', function (Blueprint $table) {
                $table->dropForeign(['id_donatur']);
            });
        }

        if (Schema::hasTable('proyeks') && Schema::hasColumn('proyeks', 'created_by')) {
            Schema::table('proyeks', function (Blueprint $table) {
                $table->dropForeign(['created_by']);
            });
        }

        // Rename users -> donatur
        Schema::rename('users', 'donatur');

        // Recreate FKs referencing donatur.id_donatur
        if (Schema::hasTable('desa')) {
            Schema::table('desa', function (Blueprint $table) {
                $table->foreign('id_admin')->references('id_donatur')->on('donatur')->nullOnDelete();
            });
        }

        if (Schema::hasTable('donasi')) {
            Schema::table('donasi', function (Blueprint $table) {
                $table->foreign('id_donatur')->references('id_donatur')->on('donatur')->cascadeOnDelete();
            });
        }
    }
};
