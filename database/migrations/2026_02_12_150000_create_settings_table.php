<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group', 50)->index();           // e.g. 'general', 'seo', 'safetrade'
            $table->string('key', 100);                      // e.g. 'site_name', 'escrow_fee_percent'
            $table->text('value')->nullable();                // stored as text, cast by type
            $table->string('type', 20)->default('string');    // string, integer, float, boolean, json, text, color, image
            $table->string('label')->nullable();              // Human-readable label
            $table->text('description')->nullable();          // Help text
            $table->json('options')->nullable();              // For select/radio: [{"value":"x","label":"X"}]
            $table->boolean('is_public')->default(false);     // Exposed to frontend API
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['group', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
