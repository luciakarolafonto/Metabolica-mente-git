<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eventi', function (Blueprint $table) {
            $table->id();

            $table->string('titolo');
            $table->string('slug')->unique();
            $table->string('sommario')->nullable();
            $table->text('descrizione')->nullable();

            $table->string('luogo');
            $table->string('ritrovo')->nullable();

            $table->dateTime('inizia_il');
            $table->dateTime('finisce_il')->nullable();

            // null = posti illimitati
            $table->unsignedInteger('posti')->nullable();

            $table->decimal('prezzo', 8, 2)->default(0);

            // bozza = non visibile sul sito | pubblicato | annullato
            $table->string('stato')->default('bozza');

            /*
             * Coupon dedicato all'evento: se attivo, chi e' registrato puo'
             * ritirarne uno solo per questo evento.
             */
            $table->boolean('coupon_attivo')->default(false);
            $table->string('coupon_titolo')->nullable();
            $table->decimal('coupon_valore', 8, 2)->nullable();
            $table->date('coupon_scadenza')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eventi');
    }
};
