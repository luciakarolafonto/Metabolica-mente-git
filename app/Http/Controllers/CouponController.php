<?php

namespace App\Http\Controllers;

use App\Mail\CouponMail;
use App\Models\Coupon;
use App\Services\CouponTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

/**
 * L'area personale: qui la persona ritira, rivede e scarica i suoi coupon.
 * Tutte queste rotte passano dai middleware 'auth' e 'verified',
 * quindi ci arriva solo chi e' collegato e ha confermato l'email.
 */
class CouponController extends Controller
{
    public function __construct(private CouponTicket $ticket)
    {
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        return view('coupon.index', [
            'coupon'        => $user->coupon,                                  // quello di prova
            'couponEventi'  => $user->coupons()->where('tipo', Coupon::TIPO_EVENTO)->with('evento')->get(),
            'prenotazioni'  => $user->prenotazioni()->with('evento')->get(),
        ]);
    }

    /**
     * Crea il coupon di prova. Uno solo per utente: la coppia
     * (user_id, ambito) e' unique sul database, quindi anche un doppio
     * click non ne crea due.
     */
    public function genera(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->coupon) {
            return redirect()->route('coupon.index')
                ->with('info', 'Hai già il tuo coupon personale: lo trovi qui sotto.');
        }

        $coupon = Coupon::creaDiProva($user);
        $giorni = config('asd.coupon.days');

        $messaggio = "Coupon creato! È valido {$giorni} giorni, fino al "
            .$coupon->expires_at->format('d/m/Y').'.';

        $messaggio .= $this->inviaMail($coupon)
            ? ' Te lo abbiamo mandato anche via email, con PDF e immagine allegati.'
            : ' Non siamo riusciti a mandartelo via email, ma puoi scaricarlo subito da qui.';

        return redirect()->route('coupon.index')->with('successo', $messaggio);
    }

    public function pdf(Request $request, Coupon $coupon): Response
    {
        $this->assicuratiCheSiaSuo($request, $coupon);

        return response($this->ticket->pdf($coupon), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$this->ticket->nomeFile($coupon, 'pdf').'"',
        ]);
    }

    public function png(Request $request, Coupon $coupon): Response
    {
        $this->assicuratiCheSiaSuo($request, $coupon);

        return response($this->ticket->png($coupon), 200, [
            'Content-Type'        => 'image/png',
            'Content-Disposition' => 'attachment; filename="'.$this->ticket->nomeFile($coupon, 'png').'"',
        ]);
    }

    /**
     * Anteprima del biglietto dentro la pagina (non scarica nulla).
     */
    public function anteprima(Request $request, Coupon $coupon): Response
    {
        $this->assicuratiCheSiaSuo($request, $coupon);

        return response($this->ticket->png($coupon), 200, [
            'Content-Type'  => 'image/png',
            'Cache-Control' => 'private, max-age=300',
        ]);
    }

    public function rinviaMail(Request $request, Coupon $coupon): RedirectResponse
    {
        $this->assicuratiCheSiaSuo($request, $coupon);

        if ($this->inviaMail($coupon)) {
            return back()->with('successo', 'Ti abbiamo rimandato il coupon a '.$request->user()->email.'.');
        }

        return back()->with('errore', 'Invio non riuscito. Intanto puoi scaricare il coupon da questa pagina.');
    }

    /**
     * Nessuno puo' scaricare il coupon di un altro.
     */
    private function assicuratiCheSiaSuo(Request $request, Coupon $coupon): void
    {
        abort_unless($coupon->user_id === $request->user()->id, 403, 'Questo coupon non è tuo.');
    }

    private function inviaMail(Coupon $coupon): bool
    {
        try {
            Mail::to($coupon->user->email)->send(new CouponMail($coupon));

            return true;
        } catch (\Throwable $e) {
            Log::error('Invio coupon fallito: '.$e->getMessage());

            return false;
        }
    }
}
