<?php

namespace App\Services;

use App\Models\Pagamento;
use App\Models\Prenotazione;
use RuntimeException;

/**
 * Tutto quello che riguarda i pagamenti passa da qui.
 *
 * OGGI: il denaro non passa dal sito. La persona sceglie come pagherà
 * (contanti, POS al ritrovo, bonifico) e il sito tiene il registro.
 *
 * DOMANI: quando l'associazione avrà un account Stripe, si mettono le
 * chiavi nel .env, si porta PAGAMENTI_ONLINE a true e si completa il
 * metodo avviaPagamentoOnline() qui sotto. Tutto il resto del sito
 * (moduli, pagine, email) è già pronto e non va toccato.
 */
class GestorePagamenti
{
    /**
     * I metodi di pagamento che ha senso proporre adesso.
     *
     * - quelli online compaiono solo se il pagamento sul sito è attivo
     * - il bonifico compare solo se l'IBAN è stato inserito nel .env
     *
     * @return array<string, array<string,mixed>>
     */
    public function metodiDisponibili(): array
    {
        $metodi = config('pagamenti.metodi', []);
        $online = (bool) config('pagamenti.online_attivo');
        $iban   = trim((string) config('pagamenti.bonifico.iban'));

        return array_filter($metodi, function (array $m, string $chiave) use ($online, $iban) {
            if (! empty($m['online']) && ! $online) {
                return false;
            }

            if ($chiave === 'bonifico' && $iban === '') {
                return false;
            }

            return true;
        }, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * Il metodo scelto è fra quelli proposti?
     */
    public function metodoValido(string $metodo): bool
    {
        return array_key_exists($metodo, $this->metodiDisponibili());
    }

    /**
     * Crea la riga di pagamento legata a una prenotazione.
     * Se l'importo è zero (coupon omaggio) nasce già come pagato.
     */
    public function creaPer(Prenotazione $prenotazione, string $metodo): Pagamento
    {
        $gratuito = (float) $prenotazione->importo <= 0;

        return Pagamento::create([
            'prenotazione_id' => $prenotazione->id,
            'user_id'         => $prenotazione->user_id,
            'codice'          => Pagamento::generaCodice('codice'),
            'importo'         => $prenotazione->importo,
            'metodo'          => $metodo,
            'stato'           => $gratuito ? Pagamento::PAGATO : Pagamento::IN_ATTESA,
            'pagato_il'       => $gratuito ? now() : null,
        ]);
    }

    /**
     * La causale da scrivere nel bonifico: contiene il codice, così
     * la trainer riconosce subito a chi appartiene il versamento.
     */
    public function causaleBonifico(Pagamento $pagamento): string
    {
        return sprintf(
            '%s - %s - %s',
            $pagamento->codice,
            $pagamento->prenotazione?->evento?->titolo ?? 'Camminata',
            $pagamento->user->full_name
        );
    }

    /**
     * Il pagamento con carta sul sito è attivo?
     */
    public function onlineAttivo(): bool
    {
        return (bool) config('pagamenti.online_attivo')
            && trim((string) config('pagamenti.stripe.chiave_segreta')) !== '';
    }

    /**
     * ------------------------------------------------------------------
     *  PUNTO DI INNESTO DI STRIPE
     * ------------------------------------------------------------------
     *
     * Qui andrà il codice che apre la pagina di pagamento di Stripe e
     * restituisce l'indirizzo a cui mandare la persona.
     *
     * Cosa servirà, quando sarà il momento:
     *   1. composer require stripe/stripe-php
     *   2. nel .env: STRIPE_KEY, STRIPE_SECRET, STRIPE_WEBHOOK_SECRET
     *      e PAGAMENTI_ONLINE=true
     *   3. creare una Checkout Session con importo e causale, salvare
     *      l'id in $pagamento->riferimento_esterno e restituire l'url
     *   4. una rotta di ritorno e un webhook che, a incasso avvenuto,
     *      chiama $pagamento->segnaPagato(null, $idTransazione)
     *
     * Finché non è pronto, il metodo si rifiuta in modo esplicito
     * invece di far finta che il pagamento sia andato a buon fine.
     */
    public function avviaPagamentoOnline(Pagamento $pagamento): string
    {
        if (! $this->onlineAttivo()) {
            throw new RuntimeException(
                'Il pagamento con carta sul sito non è ancora attivo. '.
                'Servono le chiavi di Stripe nel file .env.'
            );
        }

        throw new RuntimeException(
            'Collegamento a Stripe non ancora realizzato: '.
            'vedi le istruzioni in app/Services/GestorePagamenti.php'
        );
    }
}
