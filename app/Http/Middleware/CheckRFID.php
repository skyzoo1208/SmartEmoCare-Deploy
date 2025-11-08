<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRFID
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->session()->has('rfid_verified')) {
            return redirect('/rfid-scan');
        }
        return $next($request);
    }
}
