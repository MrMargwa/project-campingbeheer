<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['telefoonnummer', 'postcode', 'huisnummer', 'straatnaam', 'plaatsnaam', 'land']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('telefoonnummer')->nullable();
            $table->string('postcode')->nullable();
            $table->integer('huisnummer')->nullable();
            $table->string('straatnaam')->nullable();
            $table->string('plaatsnaam')->nullable();
            $table->string('land')->nullable();
        });
    }
};
