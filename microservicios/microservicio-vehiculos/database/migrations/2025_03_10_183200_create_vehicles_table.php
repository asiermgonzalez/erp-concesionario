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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('vin', 17)->unique()->comment('Vehicle Identification Number');
            $table->string('registration_number', 20)->nullable();
            $table->unsignedBigInteger('brand_id');
            $table->unsignedBigInteger('model_id');
            $table->integer('year');
            $table->string('color', 50);
            $table->integer('mileage');
            $table->decimal('price', 10, 2);
            $table->decimal('cost', 10, 2);
            $table->enum('condition', ['new', 'used', 'certified']);
            $table->enum('status', ['available', 'reserved', 'sold'])->default('available');
            $table->string('fuel_type', 50);
            $table->string('transmission', 50);
            $table->string('engine_size', 50)->nullable();
            $table->integer('power')->nullable()->comment('Engine power in HP');
            $table->integer('doors')->nullable();
            $table->integer('seats')->nullable();
            $table->text('description')->nullable();
            $table->json('features')->nullable();
            $table->text('observations')->nullable();
            $table->date('purchase_date')->nullable();
            $table->string('location')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('brand_id')->references('id')->on('brands');
            $table->foreign('model_id')->references('id')->on('vehicle_models');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
