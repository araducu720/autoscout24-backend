<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dealers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('company_name');
            $table->string('slug')->unique();
            $table->string('logo')->nullable();
            $table->text('description')->nullable();
            $table->string('address');
            $table->string('city');
            $table->string('postal_code');
            $table->string('country')->default('Germany');
            $table->string('phone');
            $table->string('email');
            $table->string('website')->nullable();
            $table->string('tax_id')->nullable(); // VAT number
            $table->string('registration_number')->nullable(); // Business registration
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->enum('type', ['independent', 'franchise', 'manufacturer'])->default('independent');
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('offers_home_delivery')->default(false);
            $table->boolean('offers_financing')->default(false);
            $table->boolean('offers_warranty')->default(false);
            $table->decimal('rating', 2, 1)->nullable();
            $table->integer('total_reviews')->default(0);
            $table->integer('total_purchases')->default(0);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['city', 'is_verified', 'is_active']);
            $table->index('is_verified');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dealers');
    }
};
