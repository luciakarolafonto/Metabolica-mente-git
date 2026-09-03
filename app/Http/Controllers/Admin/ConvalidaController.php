<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Pagina riservata alla trainer: cerca un codice coupon e lo "timbra".
 * Da quel momento il coupon risulta usato e non e' piu' riutilizzabile.
 */
class ConvalidaController extends Controller
{
    public function mostra(Request $request): View
    {
        $digitato = trim((string) $request->query('codice'));

        // La trainer puo' scrivere "2cfr78", "#2CFR78" o "2 CFR 78":
        // normalizziamo prima di cercare.
        $codice = Coupon::normalizzaCodice($digitato);

        $coupon = $codice !== ''
            ? Coupon::with('user')->where('code', $codice)->first()
            : null;

        return view('admin.convalida', [
            'codice'   => $digitato,
            'coupon'   => $coupon,
            'cercato'  => $codice !== '',
            'ultimi'   => Coupon::with('user')->latest()->limit(15)->get(),
        ]);
    }

    public function convalida(Request $request): RedirectResponse
    {
        $dati = $request->validate([
            'code' => ['required', 'string', 'exists:coupons,code'],
        ]);

        $coupon = Coupon::where('code', $dati['code'])->firstOrFail();

        if ($coupon->isUsato()) {
            return back()->with('errore', 'Coupon già utilizzato il '.$coupon->used_at->format('d/m/Y H:i').'.');
        }

        if ($coupon->isScaduto()) {
            $coupon->update(['status' => Coupon::EXPIRED]);

            return back()->with('errore', 'Coupon scaduto il '.$coupon->expires_at->format('d/m/Y').'.');
        }

        $coupon->update([
            'status'  => Coupon::USED,
            'used_at' => now(),
            'used_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('admin.convalida', ['codice' => $coupon->code])
            ->with('successo', 'Coupon di '.$coupon->user->full_name.' convalidato. Buona camminata!');
    }
}
