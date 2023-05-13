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
        Schema::create('trains', function (Blueprint $table) {
            $table->id('train_number');
            $table->tinyInteger('s_station');
            $table->tinyInteger('d_station');
            $table->integer('num_of_seats')->nullable(true);
            $table->json('seat_left')->nullable(false);
            $table->json('booked_seat')->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trains');
    }
};
