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
        if (Schema::hasTable('vehicles')) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->index('status', 'idx_vehicles_status');
                $table->index('is_featured', 'idx_vehicles_is_featured');
                $table->index('fuel_type', 'idx_vehicles_fuel_type');
                $table->index('transmission', 'idx_vehicles_transmission');
                $table->index('body_type', 'idx_vehicles_body_type');
                $table->index('condition', 'idx_vehicles_condition');
                $table->index('country', 'idx_vehicles_country');
                $table->index('created_at', 'idx_vehicles_created_at');
            });
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'is_admin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->index('is_admin', 'idx_users_is_admin');
            });
        }

        if (Schema::hasTable('conversations')) {
            Schema::table('conversations', function (Blueprint $table) {
                $table->index('buyer_id', 'idx_conversations_buyer_id');
                $table->index('seller_id', 'idx_conversations_seller_id');
            });
        }

        if (Schema::hasTable('phone_reveals')) {
            Schema::table('phone_reveals', function (Blueprint $table) {
                $table->index('created_at', 'idx_phone_reveals_created_at');
            });
        }

        if (Schema::hasTable('contact_messages')) {
            Schema::table('contact_messages', function (Blueprint $table) {
                $table->index('status', 'idx_contact_messages_status');
            });
        }

        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->index('status', 'idx_invoices_status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('vehicles')) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->dropIndex('idx_vehicles_status');
                $table->dropIndex('idx_vehicles_is_featured');
                $table->dropIndex('idx_vehicles_fuel_type');
                $table->dropIndex('idx_vehicles_transmission');
                $table->dropIndex('idx_vehicles_body_type');
                $table->dropIndex('idx_vehicles_condition');
                $table->dropIndex('idx_vehicles_country');
                $table->dropIndex('idx_vehicles_created_at');
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex('idx_users_is_admin');
            });
        }

        if (Schema::hasTable('conversations')) {
            Schema::table('conversations', function (Blueprint $table) {
                $table->dropIndex('idx_conversations_buyer_id');
                $table->dropIndex('idx_conversations_seller_id');
            });
        }

        if (Schema::hasTable('phone_reveals')) {
            Schema::table('phone_reveals', function (Blueprint $table) {
                $table->dropIndex('idx_phone_reveals_created_at');
            });
        }

        if (Schema::hasTable('contact_messages')) {
            Schema::table('contact_messages', function (Blueprint $table) {
                $table->dropIndex('idx_contact_messages_status');
            });
        }

        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropIndex('idx_invoices_status');
            });
        }
    }
};
