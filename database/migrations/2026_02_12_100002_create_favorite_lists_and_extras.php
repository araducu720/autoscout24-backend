<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Favorite Lists
        if (!Schema::hasTable('favorite_lists')) {
            Schema::create('favorite_lists', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('name', 100);
                $table->text('description')->nullable();
                $table->boolean('is_public')->default(false);
                $table->timestamps();

                $table->index('user_id');
            });
        }

        // Favorite List Items
        if (!Schema::hasTable('favorite_list_items')) {
            Schema::create('favorite_list_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('favorite_list_id')->constrained()->cascadeOnDelete();
                $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
                $table->timestamp('created_at')->useCurrent();

                $table->unique(['favorite_list_id', 'vehicle_id']);
            });
        }

        // Add sort_order to vehicles if not exists
        if (Schema::hasTable('vehicles') && !Schema::hasColumn('vehicles', 'sort_order')) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->integer('sort_order')->default(0)->after('status');
            });
        }

        // Add dispute resolution fields if not exists
        if (Schema::hasTable('disputes')) {
            if (!Schema::hasColumn('disputes', 'proposed_resolution')) {
                Schema::table('disputes', function (Blueprint $table) {
                    $table->text('proposed_resolution')->nullable()->after('description');
                    $table->boolean('buyer_accepted_resolution')->nullable()->after('proposed_resolution');
                    $table->boolean('seller_accepted_resolution')->nullable()->after('buyer_accepted_resolution');
                });
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('favorite_list_items');
        Schema::dropIfExists('favorite_lists');

        if (Schema::hasTable('vehicles') && Schema::hasColumn('vehicles', 'sort_order')) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->dropColumn('sort_order');
            });
        }

        if (Schema::hasTable('disputes')) {
            Schema::table('disputes', function (Blueprint $table) {
                if (Schema::hasColumn('disputes', 'proposed_resolution')) {
                    $table->dropColumn(['proposed_resolution', 'buyer_accepted_resolution', 'seller_accepted_resolution']);
                }
            });
        }
    }
};
