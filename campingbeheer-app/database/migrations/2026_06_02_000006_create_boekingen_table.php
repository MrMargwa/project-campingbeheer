<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('boekingen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gebruiker_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnDelete();
            $table->string('naam')->nullable();
            $table->string('email')->nullable();
            $table->string('telefoon')->nullable();
            $table->string('postcode')->nullable();
            $table->string('huisnummer')->nullable();
            $table->string('straat')->nullable();
            $table->string('plaats')->nullable();
            $table->string('land')->nullable();
            $table->foreignId('accommodatie_id')
                ->constrained('accommodaties')
                ->cascadeOnDelete();
            $table->date('aankomst_datum');
            $table->string('aankomst_tijd')->nullable();
            $table->date('vertrek_datum');
            $table->string('vertrek_tijd')->nullable();
            $table->integer('aantal_personen');
            $table->text('opmerking')->nullable();
            $table->decimal('totaal_prijs', 10, 2);
            $table->string('status');
            $table->timestamp('aangemaakt_op')->useCurrent();
            $table->timestamp('bewerkt_op')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boekingen');
    }
};