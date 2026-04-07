<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('transaction_id')->nullable()->constrained('safetrade_transactions')->onDelete('set null');

            // Rating
            $table->tinyInteger('rating')->unsigned(); // 1-5
            $table->text('comment');

            // Rating breakdown
            $table->tinyInteger('rating_vehicle')->unsigned()->nullable();
            $table->tinyInteger('rating_seller')->unsigned()->nullable();
            $table->tinyInteger('rating_shipping')->unsigned()->nullable();

            // Media
            $table->json('photos')->nullable();

            // Privacy
            $table->boolean('anonymous')->default(false);

            // Helpfulness
            $table->integer('helpful_count')->default(0);

            // Status
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved');

            $table->timestamps();

            // One review per user per vehicle
            $table->unique(['vehicle_id', 'user_id']);
            $table->index(['vehicle_id', 'status']);
            $table->index(['user_id']);
        });

        Schema::create('review_helpful', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['review_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_helpful');
        Schema::dropIfExists('reviews');
    }
};
