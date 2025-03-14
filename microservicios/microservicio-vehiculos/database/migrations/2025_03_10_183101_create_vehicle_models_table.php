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
        Schema::create('vehicle_models', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('brand_id');
            $table->string('name', 100);
            $table->string('type', 50);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
            $table->unique(['brand_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_models');
    }
};
