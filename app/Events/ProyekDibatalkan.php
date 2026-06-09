<?php

namespace App\Events;

use App\Models\Proyek;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProyekDibatalkan
{
    use Dispatchable, SerializesModels;

    public function __construct(public Proyek $proyek) {}
}
