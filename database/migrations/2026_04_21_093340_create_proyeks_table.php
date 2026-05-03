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
        Schema::create('proyeks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('desa_id')->constrained('desa', 'id_desa')->cascadeOnDelete();
            $table->foreignId('penyedia_id')->nullable()->constrained('penyedia_energis')->nullOnDelete();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->enum('jenis_energi', ['solar', 'mikro_hidro', 'lainnya'])->nullable();
            $table->date('estimasi_mulai')->nullable();
            $table->date('estimasi_selesai')->nullable();
            $table->decimal('target_dana', 15, 2)->default(0);
            $table->decimal('dana_terkumpul', 15, 2)->default(0);
            $table->enum('status', [
                'draft',
                'menunggu_konfirmasi_penyedia',
                'diterima_penyedia',
                'menunggu_review_admin',
                'aktif_funding',
                'eksekusi',
                'selesai',
                'ditolak'
            ])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proyeks');
    }
};
