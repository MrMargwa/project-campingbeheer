<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('house_number')->nullable();
            $table->string('street')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->foreignId('accommodation_id')
                ->constrained('accommodations')
                ->cascadeOnDelete();
            $table->date('arrival_date');
            $table->string('arrival_time')->nullable();
            $table->date('departure_date');
            $table->string('departure_time')->nullable();
            $table->integer('number_of_persons');
            $table->text('notes')->nullable();
            $table->decimal('total_price', 10, 2);
            $table->string('status');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};