<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verblijf_kenmerk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('verblijf_id')
                ->nullable()
                ->constrained('verblijven')
                ->nullOnDelete();
            $table->foreignId('kenmerk_id')
                ->constrained('kenmerken')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verblijf_kenmerk');
    }
};
