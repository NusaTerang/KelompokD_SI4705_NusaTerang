<?php

namespace App\Notifications;

use App\Models\Proyek;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RefundDonaturNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Proyek $proyek,
        public float $nominal
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $formattedNominal = 'Rp ' . number_format($this->nominal, 0, ',', '.');

        return [
            'title'         => 'Refund Donasi Berhasil',
            'message'       => "Dana donasi kamu sebesar {$formattedNominal} untuk proyek {$this->proyek->judul} telah dikembalikan ke saldo NusaTerang kamu.",
            'category'      => 'donation_refunded',
            'project_id'    => $this->proyek->id,
            'project_title' => $this->proyek->judul,
            'nominal'       => $this->nominal,
            'url'           => route('proyek.show', $this->proyek->id),
        ];
    }
}
