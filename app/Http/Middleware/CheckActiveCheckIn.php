<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckActiveCheckIn
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $activeCheckin = $user?->activeCheckin();

        if (! $activeCheckin) {
            return redirect()
                ->route('supervisor.checkin.index')
                ->with('error', 'Anda harus melakukan check-in selfie terlebih dahulu sebelum membuat laporan.');
        }

        $request->attributes->set('active_checkin', $activeCheckin);

        return $next($request);
    }
}
