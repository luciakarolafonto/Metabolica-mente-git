<?php

use App\Http\Middleware\SoloStaff;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'staff' => SoloStaff::class,
        ]);

        // Chi non e' collegato e prova ad aprire l'area coupon
        // finisce sulla pagina di accesso, non su una rotta inglese.
        $middleware->redirectGuestsTo(fn () => route('accesso'));
        $middleware->redirectUsersTo(fn () => route('coupon.index'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
