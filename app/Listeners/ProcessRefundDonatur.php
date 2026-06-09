<?php

namespace App\Listeners;

use App\Events\ProyekDibatalkan;
use App\Models\{Donasi, MutasiSaldo, SaldoDonatur, User};
use App\Notifications\{LaporanRefundAdmin, RefundDonaturNotification};
use App\Services\NotificationRecipientService;
use Illuminate\Support\Facades\{DB, Log};

class ProcessRefundDonatur
{
    public function __construct(
        protected NotificationRecipientService $recipientService
    ) {}

    public function handle(ProyekDibatalkan $event): void
    {
        $proyek = $event->proyek;

        $donasi = Donasi::where('id_proyek', $proyek->id)
            ->where('status', 'success')
            ->where('refund_status', Donasi::REFUND_NONE)
            ->get();

        if ($donasi->isEmpty()) {
            Log::info("Refund PBI-30: Proyek #{$proyek->id} tidak ada donasi sukses.");
            $this->notifyAdmins($proyek, 0, 0, 'Tidak ada donasi sukses yang perlu direfund.');
            return;
        }

        $grouped = $donasi->groupBy('id_donatur');
        $totalRefunded = 0;
        $jumlahDonatur = 0;
        $failedDonaturIds = [];

        foreach ($grouped as $idDonatur => $donasiGroup) {
            $nominal = $donasiGroup->sum('nominal');
            $donasiIds = $donasiGroup->pluck('id_donasi');

            try {
                DB::transaction(function () use ($idDonatur, $nominal, $proyek, $donasiGroup, $donasiIds) {
                    $saldo = SaldoDonatur::firstOrCreate(
                        ['id_donatur' => $idDonatur],
                        ['saldo' => 0]
                    );
                    SaldoDonatur::lockForUpdate()->find($saldo->id)->increment('saldo', $nominal);

                    MutasiSaldo::create([
                        'id_donatur'          => $idDonatur,
                        'referensi_proyek_id' => $proyek->id,
                        'tipe'                => 'refund',
                        'nominal'             => $nominal,
                        'keterangan'          => "Refund donasi proyek: {$proyek->judul}",
                    ]);

                    foreach ($donasiGroup as $donasi) {
                        $donasi->update([
                            'status' => 'refunded',
                            'refund_status' => Donasi::REFUND_REFUNDED,
                            'refund_amount' => $donasi->nominal,
                            'refunded_at' => now(),
                        ]);
                    }
                });

                $donatur = User::find($idDonatur);
                if ($donatur) {
                    $donatur->notify(new RefundDonaturNotification($proyek, $nominal));
                }

                $totalRefunded += $nominal;
                $jumlahDonatur++;

            } catch (\Throwable $e) {
                Log::error("Refund PBI-30 GAGAL donatur #{$idDonatur} proyek #{$proyek->id}: {$e->getMessage()}", [
                    'exception' => $e,
                    'nominal'   => $nominal,
                    'donasi_ids' => $donasiIds,
                ]);

                $this->notifyAdmins(
                    $proyek, 0, 0,
                    "GAGAL: Refund donatur #{$idDonatur} nominal Rp{$nominal}: {$e->getMessage()}"
                );

                $failedDonaturIds[] = $idDonatur;
            }
        }

        $this->notifyAdmins($proyek, $jumlahDonatur, $totalRefunded, null, $failedDonaturIds);
    }

    private function notifyAdmins(
        $proyek,
        int $jumlahDonatur,
        float $totalDana,
        ?string $errorMessage = null,
        array $failedDonaturIds = []
    ): void {
        $this->recipientService->admins()->each(
            fn ($admin) => $admin->notify(
                new LaporanRefundAdmin($proyek, $jumlahDonatur, $totalDana, $errorMessage, $failedDonaturIds)
            )
        );
    }
}
