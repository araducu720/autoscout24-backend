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
            $table->foreignId('make_id')->constrained('vehicle_makes')->onDelete('cascade');
            $table->foreignId('model_id')->constrained('vehicle_models')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('title');
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->year('year');
            $table->integer('mileage');
            $table->enum('fuel_type', ['petrol', 'diesel', 'electric', 'hybrid', 'lpg'])->default('petrol');
            $table->enum('transmission', ['manual', 'automatic'])->default('manual');
            $table->enum('body_type', ['sedan', 'suv', 'coupe', 'hatchback', 'wagon', 'convertible', 'van', 'pickup', 'truck', 'motorcycle', 'cruiser', 'motorhome', 'caravan'])->nullable();
            $table->string('color')->nullable();
            $table->integer('doors')->nullable();
            $table->integer('seats')->nullable();
            $table->integer('engine_size')->nullable()->comment('in cc');
            $table->integer('power')->nullable()->comment('in hp');
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->enum('condition', ['new', 'used'])->default('used');
            $table->enum('status', ['active', 'sold', 'inactive'])->default('active');
            $table->integer('views_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
            
            $table->index(['make_id', 'model_id', 'status']);
            $table->index(['price', 'year']);
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
