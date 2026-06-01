<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Proyek;

class PublishScheduledProyek extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proyek:publish-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mempublikasikan proyek yang sudah mencapai jadwal publikasi.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $proyeks = Proyek::where('status', 'terjadwal')
            ->whereNotNull('jadwal_publikasi')
            ->where('jadwal_publikasi', '<=', now())
            ->get();

        $count = 0;
        foreach ($proyeks as $proyek) {
            $proyek->update([
                'status' => 'aktif_funding',
                'jadwal_publikasi' => null,
            ]);
            $count++;
        }

        $this->info("Berhasil mempublikasikan {$count} proyek yang dijadwalkan.");
    }
}
