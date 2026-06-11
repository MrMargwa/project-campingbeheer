<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('accommodaties', function (Blueprint $table) {
            $table->boolean('huisdieren_toegestaan')->default(false)->after('max_personen');
        });
    }

    public function down(): void
    {
        Schema::table('accommodaties', function (Blueprint $table) {
            $table->dropColumn('huisdieren_toegestaan');
        });
    }
};
