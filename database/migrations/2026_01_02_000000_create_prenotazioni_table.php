<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prenotazioni', function (Blueprint $table) {
            $table->id();

            $table->foreignId('evento_id')->constrained('eventi')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Codice da mostrare alla trainer, stesso stile dei coupon
            $table->string('codice')->unique();

            $table->unsignedInteger('posti')->default(1);
            $table->decimal('importo', 8, 2)->default(0);

            // contanti | carta | bonifico  -> la trainer lo vede in elenco
            $table->string('metodo')->default('contanti');

            $table->boolean('pagato')->default(false);
            $table->timestamp('pagato_il')->nullable();

            // confermata | annullata
            $table->string('stato')->default('confermata');
            $table->timestamp('annullata_il')->nullable();

            // Quando e' partito il promemoria "la camminata e' oggi".
            // Serve a non mandarlo due volte alla stessa persona.
            $table->timestamp('promemoria_inviato_il')->nullable();

            // Coupon eventualmente usato per questa prenotazione
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->nullOnDelete();

            $table->text('note')->nullable();

            $table->timestamps();

            // Una persona prenota una volta sola per evento.
            $table->unique(['evento_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prenotazioni');
    }
};
