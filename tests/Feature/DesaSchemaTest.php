<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DesaSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_desa_table_has_status_verifikasi_column_for_dummy_seeder(): void
    {
        $this->assertTrue(Schema::hasColumn('desa', 'status_verifikasi'));
    }
}
