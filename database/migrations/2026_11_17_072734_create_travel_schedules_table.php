<?php

use App\Models\Location;
use App\Models\TravelSchedule;
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
        Schema::create('travel_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Location::class);
            $table->foreignIdFor(TravelSchedule::class);
            $table->integer('order', false, true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_schedules');
    }
};
