<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('accommodations', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_nl')->nullable();
            $table->string('title_en')->nullable();
            $table->string('title_de')->nullable();
            $table->string('title_fy')->nullable();
            $table->string('type');
            $table->string('type_nl')->nullable();
            $table->string('type_en')->nullable();
            $table->string('type_de')->nullable();
            $table->string('type_fy')->nullable();
            $table->text('description');
            $table->text('description_nl')->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_de')->nullable();
            $table->text('description_fy')->nullable();
            $table->bigInteger('min_persons');
            $table->bigInteger('max_persons');
            $table->decimal('price_per_night', 10, 2);
            $table->string('image')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->enum('status', ['available', 'unavailable']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accommodations');
    }
};