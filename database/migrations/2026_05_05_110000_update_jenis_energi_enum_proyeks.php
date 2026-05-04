<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Expand enum to include both old and new values before data migration
        DB::statement("ALTER TABLE proyeks MODIFY jenis_energi ENUM('solar','mikro_hidro','lainnya','panel_surya','biogas','hybrid_solar_baterai') NULL");

        DB::statement("UPDATE proyeks SET jenis_energi = 'panel_surya' WHERE jenis_energi = 'solar'");
        DB::statement("UPDATE proyeks SET jenis_energi = 'biogas' WHERE jenis_energi = 'lainnya'");

        // Set final enum — old values no longer exist in data
        DB::statement("ALTER TABLE proyeks MODIFY jenis_energi ENUM('panel_surya','mikro_hidro','biogas','hybrid_solar_baterai') NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE proyeks MODIFY jenis_energi ENUM('panel_surya','mikro_hidro','biogas','hybrid_solar_baterai','solar','lainnya') NULL");
        DB::statement("UPDATE proyeks SET jenis_energi = 'solar' WHERE jenis_energi = 'panel_surya'");
        DB::statement("UPDATE proyeks SET jenis_energi = 'lainnya' WHERE jenis_energi IN ('biogas','hybrid_solar_baterai')");
        DB::statement("ALTER TABLE proyeks MODIFY jenis_energi ENUM('solar','mikro_hidro','lainnya') NULL");
    }
};
