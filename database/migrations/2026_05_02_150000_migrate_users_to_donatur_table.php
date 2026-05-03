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

        // Drop FK on desa.id_admin -> users
        if (Schema::hasTable('desa')) {
            Schema::table('desa', function (Blueprint $table) {
                $table->dropForeign(['id_admin']);
            });
        }

        // Drop FK on donasi.user_id -> users
        if (Schema::hasTable('donasi')) {
            Schema::table('donasi', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
        }

        // Drop FK on proyeks.created_by -> users
        if (Schema::hasTable('proyeks')) {
            Schema::table('proyeks', function (Blueprint $table) {
                $table->dropForeign(['created_by']);
            });
        }

        // Drop FK on users.penyedia_id -> penyedia_energis (must be done before dropping users)
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'penyedia_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['penyedia_id']);
            });
        }

        Schema::create('donatur', function (Blueprint $table) {
            $table->id('id_donatur');
            $table->string('nama', 100);
            $table->string('email', 100)->unique();
            $table->string('password', 255);
            $table->string('no_telepon', 20)->nullable();
            $table->enum('role', ['admin', 'penyedia', 'donatur'])->default('donatur');
            $table->unsignedBigInteger('penyedia_id')->nullable();
            $table->rememberToken();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();

            $table->foreign('penyedia_id')->references('id')->on('penyedia_energis')->nullOnDelete();
        });

        if (Schema::hasTable('users')) {
            $rows = DB::table('users')->orderBy('id')->get();
            foreach ($rows as $u) {
                $a = (array) $u;
                DB::table('donatur')->insert([
                    'id_donatur' => $a['id'],
                    'nama' => $a['name'] ?? $a['nama'],
                    'email' => $a['email'],
                    'password' => $a['password'],
                    'no_telepon' => $a['no_telepon'] ?? $a['phone'] ?? null,
                    'role' => $a['role'] ?? 'donatur',
                    'penyedia_id' => $a['penyedia_id'] ?? null,
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
