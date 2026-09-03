<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Lascia passare solo gli utenti con is_staff = true (la trainer).
 * Serve a proteggere la pagina di convalida dei coupon.
 */
class SoloStaff
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->is_staff) {
            abort(403, 'Questa pagina è riservata allo staff dell\'associazione.');
        }

        return $next($request);
    }
}
