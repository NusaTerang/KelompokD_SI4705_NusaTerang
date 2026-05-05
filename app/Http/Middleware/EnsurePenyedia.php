<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePenyedia
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check() || ! auth()->user()->isPenyedia()) {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk Penyedia Energi.');
        }

        return $next($request);
    }
}
