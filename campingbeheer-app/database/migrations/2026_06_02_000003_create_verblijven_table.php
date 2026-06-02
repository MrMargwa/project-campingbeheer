<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verblijven', function (Blueprint $table) {
            $table->id();
            $table->string('titel');
            $table->string('type');
            $table->text('beschrijving');
            $table->bigInteger('max_personen');
            $table->decimal('prijs_per_nacht', 10, 2);
            $table->string('afbeelding');
            $table->boolean('actief');
            $table->timestamp('aangemaakt_op')->useCurrent();
            $table->timestamp('bewerkt_op')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verblijven');
    }
};
