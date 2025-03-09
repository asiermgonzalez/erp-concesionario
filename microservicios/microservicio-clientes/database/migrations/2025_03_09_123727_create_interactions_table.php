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
        Schema::create('interactions', function (Blueprint $table) {
            $table->id();
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('user_id');
            $table->string('interaction_type', 50);
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status', 50);
            $table->dateTime('scheduled_date');
            $table->dateTime('completed_date')->nullable();
            $table->timestamps();
            $table->softDeletes();                    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interactions');
    }
};
