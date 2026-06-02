<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('naam');
            $table->string('email');
            $table->string('telefoonnummer')->nullable();
            $table->string('postcode')->nullable();
            $table->integer('huisnummer')->nullable();
            $table->string('straatnaam')->nullable();
            $table->string('plaatsnaam')->nullable();
            $table->string('land')->nullable();
            $table->string('wachtwoord');
            $table->enum('rol', ['admin', 'gast']);
            $table->timestamp('aangemaakt_op')->useCurrent();
            $table->timestamp('bewerkt_op')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};