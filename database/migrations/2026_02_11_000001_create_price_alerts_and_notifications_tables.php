<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('vehicle_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('saved_search_id')->nullable()->constrained()->onDelete('cascade');
            $table->decimal('target_price', 12, 2);
            $table->decimal('current_price', 12, 2)->nullable();
            $table->enum('alert_type', ['below', 'above', 'change', 'drop_percent'])->default('below');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_triggered_at')->nullable();
            $table->integer('triggered_count')->default(0);
            $table->boolean('notify_email')->default(true);
            $table->boolean('notify_push')->default(true);
            $table->timestamps();

            $table->index(['user_id', 'is_active']);
            $table->index(['vehicle_id', 'is_active']);
        });

        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('channel', ['email', 'push', 'sms'])->default('email');
            $table->boolean('bid_received')->default(true);
            $table->boolean('bid_accepted')->default(true);
            $table->boolean('bid_rejected')->default(true);
            $table->boolean('payment_received')->default(true);
            $table->boolean('payment_verified')->default(true);
            $table->boolean('transaction_update')->default(true);
            $table->boolean('message_received')->default(true);
            $table->boolean('dispute_update')->default(true);
            $table->boolean('price_alert')->default(true);
            $table->boolean('new_listing_match')->default(true);
            $table->boolean('pickup_reminder')->default(true);
            $table->boolean('marketing')->default(false);
            $table->boolean('weekly_digest')->default(true);
            $table->timestamps();

            $table->unique(['user_id', 'channel']);
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action');
            $table->string('auditable_type')->nullable();
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at');

            $table->index(['auditable_type', 'auditable_id']);
            $table->index(['user_id', 'action']);
            $table->index('created_at');
        });

        // Add verification fields to users table
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('identity_verified')->default(false)->after('bank_details_verified_at');
            $table->timestamp('identity_verified_at')->nullable()->after('identity_verified');
            $table->json('verification_documents')->nullable()->after('identity_verified_at');
            $table->integer('trust_score')->default(0)->after('verification_documents');
            $table->string('two_factor_secret', 100)->nullable()->after('trust_score');
            $table->boolean('two_factor_enabled')->default(false)->after('two_factor_secret');
        });

        // Add price history tracking to vehicles
        Schema::table('vehicles', function (Blueprint $table) {
            $table->json('price_history')->nullable()->after('price');
            $table->decimal('original_price', 12, 2)->nullable()->after('price_history');
            $table->integer('price_drops_count')->default(0)->after('original_price');
            $table->timestamp('price_last_changed_at')->nullable()->after('price_drops_count');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_alerts');
        Schema::dropIfExists('notification_preferences');
        Schema::dropIfExists('audit_logs');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'identity_verified', 'identity_verified_at',
                'verification_documents', 'trust_score',
                'two_factor_secret', 'two_factor_enabled',
            ]);
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn([
                'price_history', 'original_price',
                'price_drops_count', 'price_last_changed_at',
            ]);
        });
    }
};
