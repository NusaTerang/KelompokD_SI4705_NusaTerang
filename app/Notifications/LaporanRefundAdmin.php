<?php

namespace App\Notifications;

use App\Models\Proyek;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LaporanRefundAdmin extends Notification
{
    use Queueable;

    public function __construct(
        public Proyek $proyek,
        public int $jumlahDonatur,
        public float $totalDana,
        public ?string $errorMessage = null,
        public array $failedDonaturIds = []
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $formattedTotal = 'Rp ' . number_format($this->totalDana, 0, ',', '.');

        if ($this->errorMessage) {
            $title   = 'Error Proses Refund';
            $message = "[Refund Error] Proyek {$this->proyek->judul}: {$this->errorMessage}";
        } else {
            $failedNote = count($this->failedDonaturIds) > 0
                ? ' (' . count($this->failedDonaturIds) . ' donatur gagal diproses)'
                : '';
            $title   = 'Laporan Refund Proyek';
            $message = "Refund selesai untuk proyek {$this->proyek->judul}. "
                . "{$this->jumlahDonatur} donatur di-refund, total dana {$formattedTotal}.{$failedNote}";
        }

        return [
            'title'         => $title,
            'message'       => $message,
            'category'      => 'refund_report',
            'project_id'    => $this->proyek->id,
            'project_title' => $this->proyek->judul,
            'url'           => route('proyek.admin.show', $this->proyek->id),
        ];
    }
}
