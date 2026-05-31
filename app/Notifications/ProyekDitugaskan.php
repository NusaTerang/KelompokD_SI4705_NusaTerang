<?php

namespace App\Notifications;

use App\Models\PenugasanProyek;
use App\Models\Proyek;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProyekDitugaskan extends Notification
{
    use Queueable;

    public function __construct(
        public Proyek $proyek,
        public ?PenugasanProyek $penugasan = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Proyek Ditugaskan',
            'message' => "Proyek {$this->proyek->judul} telah ditugaskan kepada Anda.",
            'category' => 'project_assignment',
            'project_id' => $this->proyek->id,
            'project_title' => $this->proyek->judul,
            'url' => $this->penugasan
                ? route('vendor.proyek.show', $this->penugasan->id_penugasan)
                : route('vendor.proyek.index'),
        ];
    }
}
