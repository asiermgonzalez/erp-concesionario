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
        Schema::create('technical_specifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_id')->unique();
            $table->string('engine_type')->nullable();
            $table->integer('displacement')->nullable()->comment('Engine displacement in cc');
            $table->integer('cylinders')->nullable();
            $table->integer('valves')->nullable();
            $table->integer('power')->nullable()->comment('Engine power in HP');
            $table->integer('torque')->nullable()->comment('Engine torque in Nm');
            $table->float('acceleration')->nullable()->comment('0-100 km/h in seconds');
            $table->integer('max_speed')->nullable()->comment('Maximum speed in km/h');
            $table->float('fuel_consumption_urban')->nullable()->comment('L/100km');
            $table->float('fuel_consumption_extra')->nullable()->comment('L/100km');
            $table->float('fuel_consumption_combined')->nullable()->comment('L/100km');
            $table->integer('co2_emissions')->nullable()->comment('CO2 emissions in g/km');
            $table->string('emission_standard')->nullable();
            $table->integer('weight')->nullable()->comment('Vehicle weight in kg');
            $table->integer('length')->nullable()->comment('Vehicle length in mm');
            $table->integer('width')->nullable()->comment('Vehicle width in mm');
            $table->integer('height')->nullable()->comment('Vehicle height in mm');
            $table->integer('wheelbase')->nullable()->comment('Wheelbase in mm');
            $table->integer('trunk_capacity')->nullable()->comment('Trunk capacity in liters');
            $table->integer('tank_capacity')->nullable()->comment('Fuel tank capacity in liters');
            $table->string('tires')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technical_specifications');
    }
};
