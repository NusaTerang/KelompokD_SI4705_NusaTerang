<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $isMysql = DB::getDriverName() === 'mysql';

        if ($isMysql) {
            DB::statement("ALTER TABLE penyedia_energis MODIFY spesialisasi ENUM('solar','mikro_hidro','lainnya','panel_surya','biogas','hybrid_solar_baterai') NOT NULL");
        }

        DB::statement("UPDATE penyedia_energis SET spesialisasi = 'panel_surya' WHERE spesialisasi = 'solar'");
        DB::statement("UPDATE penyedia_energis SET spesialisasi = 'biogas' WHERE spesialisasi = 'lainnya'");

        if ($isMysql) {
            DB::statement("ALTER TABLE penyedia_energis MODIFY spesialisasi ENUM('panel_surya','mikro_hidro','biogas','hybrid_solar_baterai') NOT NULL");
        }
    }

    public function down(): void
    {
        $isMysql = DB::getDriverName() === 'mysql';

        if ($isMysql) {
            DB::statement("ALTER TABLE penyedia_energis MODIFY spesialisasi ENUM('panel_surya','mikro_hidro','biogas','hybrid_solar_baterai','solar','lainnya') NOT NULL");
        }

        DB::statement("UPDATE penyedia_energis SET spesialisasi = 'solar' WHERE spesialisasi = 'panel_surya'");
        DB::statement("UPDATE penyedia_energis SET spesialisasi = 'lainnya' WHERE spesialisasi IN ('biogas','hybrid_solar_baterai')");

        if ($isMysql) {
            DB::statement("ALTER TABLE penyedia_energis MODIFY spesialisasi ENUM('solar','mikro_hidro','lainnya') NOT NULL");
        }
    }
};
