<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('accommodation_feature', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accommodation_id')
                ->nullable()
                ->constrained('accommodations')
                ->nullOnDelete();
            $table->foreignId('feature_id')
                ->constrained('features')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accommodation_feature');
    }
};