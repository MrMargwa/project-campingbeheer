<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('boekingen', function (Blueprint $table) {
            $table->enum('aankomst_tijd', ['ochtend', 'middag'])->nullable()->after('aankomst_datum');
            $table->enum('vertrek_tijd', ['ochtend', 'middag'])->nullable()->after('vertrek_datum');
        });
    }

    public function down(): void
    {
        Schema::table('boekingen', function (Blueprint $table) {
            $table->dropColumn(['aankomst_tijd', 'vertrek_tijd']);
        });
    }
};
