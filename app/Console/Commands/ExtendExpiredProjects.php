<?php

namespace App\Console\Commands;

use App\Models\PenugasanProyek;
use App\Models\Proyek;
use Illuminate\Console\Command;

class ExtendExpiredProjects extends Command
{
    protected $signature = 'proyek:extend-expired';

    protected $description = 'Extend expired active-funding projects by 30 days and request vendor decision.';

    public function handle(): int
    {
        $count = 0;

        Proyek::query()
            ->where('status', 'aktif_funding')
            ->whereDate('estimasi_selesai', '<', now()->toDateString())
            ->where('expired_extension_pending', false)
            ->chunkById(100, function ($proyeks) use (&$count) {
                foreach ($proyeks as $proyek) {
                    $proyek->update([
                        'expired_original_end_date' => $proyek->estimasi_selesai,
                        'estimasi_selesai' => now()->addDays(30)->toDateString(),
                        'expired_extended_at' => now(),
                        'expired_extension_pending' => true,
                        'expired_vendor_decision' => null,
                        'status' => 'menunggu_keputusan_vendor',
                    ]);

                    if ($proyek->penyedia_id) {
                        PenugasanProyek::firstOrCreate(
                            [
                                'id_proyek' => $proyek->id,
                                'id_penyedia' => $proyek->penyedia_id,
                            ],
                            [
                                'status_penugasan' => 'diterima',
                                'tanggal_respon' => now(),
                            ]
                        );
                    }

                    $count++;
                }
            });

        $this->info("Extended {$count} expired project(s).");

        return self::SUCCESS;
    }
}
