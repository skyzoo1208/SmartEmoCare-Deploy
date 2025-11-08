<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RfidMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah berhasil scan RFID
        if (!session()->has('rfid_verified')) {
            return redirect()->route('rfid.scan');
        }

        return $next($request);
    }
}
