<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('progress_proyek_vendor', function (Blueprint $table) {
            $table->bigIncrements('id_progress');
            $table->unsignedBigInteger('id_penugasan');
            $table->unsignedTinyInteger('persentase')->default(0);
            $table->text('deskripsi')->nullable();
            $table->json('foto_paths')->nullable();
            $table->enum('status_progress', ['dijadwalkan', 'berjalan', 'selesai'])->default('berjalan');
            $table->enum('status', ['draft', 'submitted'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->foreign('id_penugasan')
                ->references('id_penugasan')
                ->on('penugasan_proyek')
                ->cascadeOnDelete();

            $table->index(['id_penugasan', 'status']);
            $table->index(['status', 'submitted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progress_proyek_vendor');
    }
};
