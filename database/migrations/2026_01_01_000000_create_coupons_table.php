<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Codice mostrato alla trainer, es. #2CFR78
            $table->string('code')->unique();

            // 'prova' = il coupon gratuito di benvenuto (uno solo per persona)
            // 'evento' = coupon legato a un evento specifico
            $table->string('tipo')->default('prova');
            $table->foreignId('evento_id')->nullable()->constrained('eventi')->cascadeOnDelete();

            /*
             * "ambito" serve solo a garantire l'unicita': vale 'prova' per il
             * coupon di benvenuto e 'evento-7' per quello del settimo evento.
             * Insieme a user_id forma una coppia unica, quindi il database
             * stesso impedisce a una persona di ritirare due volte lo stesso
             * coupon. Funziona su qualsiasi database, anche su SQLite.
             */
            $table->string('ambito');

            // Copiati al momento della creazione: cosi' il biglietto resta
            // com'era anche se poi l'evento viene modificato.
            $table->string('titolo');
            $table->decimal('valore', 8, 2)->default(0);

            $table->string('status')->default('active'); // active | used | expired
            $table->timestamp('issued_at');
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->foreignId('used_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'ambito']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
