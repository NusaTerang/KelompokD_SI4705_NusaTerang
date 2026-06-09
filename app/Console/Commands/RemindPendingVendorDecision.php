<?php

namespace App\Console\Commands;

use App\Models\Proyek;
use App\Models\User;
use App\Notifications\ProyekDitugaskan;
use Illuminate\Console\Command;

class RemindPendingVendorDecision extends Command
{
    protected $signature = 'proyek:remind-pending-decision';

    protected $description = 'Remind vendors who have not yet submitted an expiry decision for projects awaiting their response.';

    public function handle(): int
    {
        $threshold = now()->subDays(3);

        Proyek::query()
            ->where('status', 'menunggu_keputusan_vendor')
            ->where('expired_extension_pending', true)
            ->where('expired_extended_at', '<=', $threshold)
            ->chunkById(100, function ($proyeks) {
                foreach ($proyeks as $proyek) {
                    if (! $proyek->penyedia_id) {
                        continue;
                    }

                    $vendorUser = User::where('penyedia_id', $proyek->penyedia_id)->first();

                    if ($vendorUser) {
                        $vendorUser->notify(new ProyekDitugaskan($proyek));
                    }
                }
            });

        $this->info('Vendor decision reminders sent.');

        return self::SUCCESS;
    }
}
