<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagamenti', function (Blueprint $table) {
            $table->id();

            // Ogni prenotazione ha un solo pagamento.
            $table->foreignId('prenotazione_id')->unique()->constrained('prenotazioni')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Riferimento da citare nel bonifico o alla trainer, es. #4KQ2WD
            $table->string('codice')->unique();

            $table->decimal('importo', 8, 2);

            // contanti | carta | bonifico | carta_online
            $table->string('metodo');

            // in_attesa | pagato | annullato | rimborsato
            $table->string('stato')->default('in_attesa');

            $table->timestamp('pagato_il')->nullable();

            // Chi ha registrato l'incasso (la trainer), quando avviene di persona
            $table->foreignId('registrato_da')->nullable()->constrained('users')->nullOnDelete();

            /*
             * Spazio per il pagamento online: qui finiranno l'identificativo
             * della sessione Stripe e la risposta del servizio. Restano vuoti
             * finché il pagamento con carta sul sito non viene attivato.
             */
            $table->string('fornitore')->nullable();      // es. 'stripe'
            $table->string('riferimento_esterno')->nullable()->index();
            $table->text('dati_fornitore')->nullable();

            $table->text('note')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagamenti');
    }
};
