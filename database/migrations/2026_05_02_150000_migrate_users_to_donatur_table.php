<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Mengganti tabel users dengan donatur sesuai ERD:
 * id_donatur, nama, email, password, no_telepon, created_at.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('donatur')) {
            return;
        }

        if (Schema::hasTable('desa')) {
            Schema::table('desa', function (Blueprint $table) {
                $table->dropForeign(['id_admin']);
            });
        }

        if (Schema::hasTable('donasi')) {
            Schema::table('donasi', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
        }

        Schema::create('donatur', function (Blueprint $table) {
            $table->id('id_donatur');
            $table->string('nama', 100);
            $table->string('email', 100)->unique();
            $table->string('password', 255);
            $table->string('no_telepon', 20)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        if (Schema::hasTable('users')) {
            $rows = DB::table('users')->orderBy('id')->get();
            foreach ($rows as $u) {
                $a = (array) $u;
                DB::table('donatur')->insert([
                    'id_donatur' => $a['id'],
                    'nama' => $a['name'],
                    'email' => $a['email'],
                    'password' => $a['password'],
                    'no_telepon' => $a['no_telepon'] ?? $a['phone'] ?? null,
                    'created_at' => $a['created_at'] ?? now(),
                ]);
            }
            Schema::drop('users');
        }

        if (Schema::hasTable('desa')) {
            Schema::table('desa', function (Blueprint $table) {
                $table->foreign('id_admin')->references('id_donatur')->on('donatur')->nullOnDelete();
            });
        }

        if (Schema::hasTable('donasi')) {
            Schema::table('donasi', function (Blueprint $table) {
                $table->renameColumn('user_id', 'id_donatur');
            });
            Schema::table('donasi', function (Blueprint $table) {
                $table->foreign('id_donatur')->references('id_donatur')->on('donatur')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        // Rollback manual disarankan lewat backup DB.
    }
};
