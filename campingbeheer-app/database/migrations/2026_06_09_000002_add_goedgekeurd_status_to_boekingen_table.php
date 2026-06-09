<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('boekingen', function (Blueprint $table) {
            $table->string('status', 20)->change();
        });
    }

    public function down(): void
    {
        Schema::table('boekingen', function (Blueprint $table) {
            $table->enum('status', ['gereed', 'in_afwachting', 'geannuleerd'])->change();
        });
    }
};
