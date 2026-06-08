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
        Schema::table('boekingen', function (Blueprint $table) {
            $table->string('naam')->nullable()->after('gebruiker_id');
            $table->string('email')->nullable()->after('naam');
            $table->string('telefoon')->nullable()->after('email');
            $table->string('postcode')->nullable()->after('telefoon');
            $table->string('huisnummer')->nullable()->after('postcode');
            $table->string('straat')->nullable()->after('huisnummer');
            $table->string('plaats')->nullable()->after('straat');
            $table->string('land')->nullable()->after('plaats');
            $table->text('opmerking')->nullable()->after('aantal_personen');
            $table->foreignId('gebruiker_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('boekingen', function (Blueprint $table) {
            $table->dropColumn(['naam', 'email', 'telefoon', 'postcode', 'huisnummer', 'straat', 'plaats', 'land', 'opmerking']);
            $table->foreignId('gebruiker_id')->nullable(false)->change();
        });
    }
};
