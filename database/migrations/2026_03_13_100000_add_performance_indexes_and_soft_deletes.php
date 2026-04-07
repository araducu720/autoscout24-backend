<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add soft deletes to vehicles
        if (!Schema::hasColumn('vehicles', 'deleted_at')) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Performance indexes for vehicles
        Schema::table('vehicles', function (Blueprint $table) {
            // Composite index for user's vehicles list
            if (!$this->hasIndex('vehicles', 'vehicles_user_id_status_created_at_index')) {
                $table->index(['user_id', 'status', 'created_at'], 'vehicles_user_id_status_created_at_index');
            }
        });

        // Performance indexes for safetrade_transactions
        if (Schema::hasTable('safetrade_transactions')) {
            Schema::table('safetrade_transactions', function (Blueprint $table) {
                if (!$this->hasIndex('safetrade_transactions', 'st_buyer_created_index')) {
                    $table->index(['buyer_id', 'created_at'], 'st_buyer_created_index');
                }
                if (!$this->hasIndex('safetrade_transactions', 'st_seller_created_index')) {
                    $table->index(['seller_id', 'created_at'], 'st_seller_created_index');
                }
            });
        }

        // Performance indexes for orders
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (!$this->hasIndex('orders', 'orders_buyer_created_index')) {
                    $table->index(['buyer_id', 'created_at'], 'orders_buyer_created_index');
                }
                if (!$this->hasIndex('orders', 'orders_seller_created_index')) {
                    $table->index(['seller_id', 'created_at'], 'orders_seller_created_index');
                }
            });
        }

        // Full-text search index on vehicles (MySQL only — SQLite doesn't support fulltext)
        if (DB::connection()->getDriverName() === 'mysql') {
            Schema::table('vehicles', function (Blueprint $table) {
                if (!$this->hasIndex('vehicles', 'vehicles_fulltext_search')) {
                    $table->fullText(['title', 'description'], 'vehicles_fulltext_search');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropIndex('vehicles_user_id_status_created_at_index');
        });

        if (DB::connection()->getDriverName() === 'mysql') {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->dropFullText('vehicles_fulltext_search');
            });
        }

        if (Schema::hasTable('safetrade_transactions')) {
            Schema::table('safetrade_transactions', function (Blueprint $table) {
                $table->dropIndex('st_buyer_created_index');
                $table->dropIndex('st_seller_created_index');
            });
        }

        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropIndex('orders_buyer_created_index');
                $table->dropIndex('orders_seller_created_index');
            });
        }
    }

    private function hasIndex(string $table, string $indexName): bool
    {
        $indexes = Schema::getIndexes($table);
        foreach ($indexes as $index) {
            if ($index['name'] === $indexName) {
                return true;
            }
        }
        return false;
    }
};
