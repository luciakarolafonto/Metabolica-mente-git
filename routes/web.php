<?php

use App\Http\Controllers\Admin\ConvalidaController;
use App\Http\Controllers\Admin\EventoController as AdminEventoController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\PagamentoController;
use App\Http\Controllers\Auth\AccessoController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\RegistrazioneController;
use App\Http\Controllers\Auth\VerificaEmailController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\PaginaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
|  PAGINE PUBBLICHE
|--------------------------------------------------------------------------
*/

Route::get('/', [PaginaController::class, 'home'])->name('home');
Route::get('/camminata-metabolica', [PaginaController::class, 'metodo'])->name('metodo');
Route::get('/chi-siamo', [PaginaController::class, 'chiSiamo'])->name('chi-siamo');
Route::get('/contatti', [PaginaController::class, 'contatti'])->name('contatti');
Route::post('/contatti', [PaginaController::class, 'inviaContatto'])
    ->middleware('throttle:5,10')
    ->name('contatti.invia');

/*
|--------------------------------------------------------------------------
|  REGISTRAZIONE E ACCESSO  (solo per chi NON e' gia' collegato)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/registrati', [RegistrazioneController::class, 'mostra'])->name('registrazione');
    Route::post('/registrati', [RegistrazioneController::class, 'registra'])->name('registrazione.salva');

    Route::get('/accedi', [AccessoController::class, 'mostra'])->name('accesso');
    Route::post('/accedi', [AccessoController::class, 'entra'])->name('accesso.entra');

    Route::get('/password-dimenticata', [PasswordController::class, 'mostraRichiesta'])->name('password.request');
    Route::post('/password-dimenticata', [PasswordController::class, 'inviaLink'])
        ->middleware('throttle:5,10')
        ->name('password.email');

    Route::get('/nuova-password/{token}', [PasswordController::class, 'mostraReset'])->name('password.reset');
    Route::post('/nuova-password', [PasswordController::class, 'salvaNuova'])->name('password.update');
});

Route::post('/esci', [AccessoController::class, 'esci'])->middleware('auth')->name('esci');

/*
|--------------------------------------------------------------------------
|  CONFERMA DELL'INDIRIZZO EMAIL
|--------------------------------------------------------------------------
|  I nomi verification.* sono quelli che Laravel si aspetta internamente:
|  li teniamo cosi' anche se gli indirizzi sono in italiano.
*/

Route::middleware('auth')->group(function () {
    Route::get('/conferma-email', [VerificaEmailController::class, 'avviso'])
        ->name('verification.notice');

    Route::get('/conferma-email/{id}/{hash}', [VerificaEmailController::class, 'conferma'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('/conferma-email/rinvia', [VerificaEmailController::class, 'rinvia'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

/*
|--------------------------------------------------------------------------
|  AREA COUPON  (serve essere collegati E avere l'email confermata)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/area-personale', [CouponController::class, 'index'])->name('coupon.index');
    Route::post('/area-personale/genera-coupon', [CouponController::class, 'genera'])->name('coupon.genera');

    Route::get('/coupon/{coupon}/anteprima.png', [CouponController::class, 'anteprima'])->name('coupon.anteprima');
    Route::get('/coupon/{coupon}/scarica-pdf', [CouponController::class, 'pdf'])->name('coupon.pdf');
    Route::get('/coupon/{coupon}/scarica-png', [CouponController::class, 'png'])->name('coupon.png');
    Route::post('/coupon/{coupon}/rinvia-mail', [CouponController::class, 'rinviaMail'])
        ->middleware('throttle:3,10')
        ->name('coupon.rinvia');
});

/*
|--------------------------------------------------------------------------
|  PAGAMENTI  (solo i propri, serve essere collegati)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/i-miei-pagamenti', [PagamentoController::class, 'index'])->name('pagamenti.index');
    Route::get('/pagamenti/{pagamento}', [PagamentoController::class, 'show'])->name('pagamenti.show');
    Route::post('/pagamenti/{pagamento}/metodo', [PagamentoController::class, 'cambiaMetodo'])->name('pagamenti.metodo');
    Route::post('/pagamenti/{pagamento}/paga', [PagamentoController::class, 'paga'])->name('pagamenti.paga');
});

/*
|--------------------------------------------------------------------------
|  APPUNTAMENTI  (elenco e dettaglio pubblici, prenotazione da collegati)
|--------------------------------------------------------------------------
*/

Route::get('/appuntamenti', [EventoController::class, 'index'])->name('eventi.index');
Route::get('/appuntamenti/{evento}', [EventoController::class, 'show'])->name('eventi.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/appuntamenti/{evento}/prenota', [EventoController::class, 'prenota'])->name('eventi.prenota');
    Route::post('/appuntamenti/{evento}/annulla', [EventoController::class, 'annulla'])->name('eventi.annulla');
    Route::post('/appuntamenti/{evento}/coupon', [EventoController::class, 'ritiraCoupon'])->name('eventi.coupon');
});

/*
|--------------------------------------------------------------------------
|  AREA STAFF  (la trainer gestisce appuntamenti, prenotazioni e coupon)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'staff'])->prefix('staff')->group(function () {
    Route::get('/convalida', [ConvalidaController::class, 'mostra'])->name('admin.convalida');
    Route::post('/convalida', [ConvalidaController::class, 'convalida'])->name('admin.convalida.salva');

    Route::get('/appuntamenti', [AdminEventoController::class, 'index'])->name('admin.eventi.index');
    Route::get('/appuntamenti/nuovo', [AdminEventoController::class, 'create'])->name('admin.eventi.create');
    Route::post('/appuntamenti', [AdminEventoController::class, 'store'])->name('admin.eventi.store');
    Route::get('/appuntamenti/{evento}/modifica', [AdminEventoController::class, 'edit'])->name('admin.eventi.edit');
    Route::put('/appuntamenti/{evento}', [AdminEventoController::class, 'update'])->name('admin.eventi.update');
    Route::delete('/appuntamenti/{evento}', [AdminEventoController::class, 'destroy'])->name('admin.eventi.destroy');

    Route::get('/appuntamenti/{evento}/partecipanti', [AdminEventoController::class, 'partecipanti'])
        ->name('admin.eventi.partecipanti');
    Route::post('/prenotazioni/{prenotazione}/pagamento', [AdminEventoController::class, 'segnaPagamento'])
        ->name('admin.prenotazioni.pagamento');
});
