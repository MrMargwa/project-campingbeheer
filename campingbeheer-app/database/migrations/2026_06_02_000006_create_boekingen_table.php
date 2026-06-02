<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('boekingen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gebruiker_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('verblijf_id')
                ->constrained('verblijven')
                ->cascadeOnDelete();
            $table->date('aankomst_datum');
            $table->date('vertrek_datum');
            $table->integer('aantal_personen');
            $table->decimal('totaal_prijs', 10, 2);
            $table->enum('status', ['gereed', 'in_afwachting', 'geannuleerd']);
            $table->timestamp('aangemaakt_op')->useCurrent();
            $table->timestamp('bewerkt_op')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boekingen');
    }
};