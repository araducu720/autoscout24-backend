<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('disputes', function (Blueprint $table) {
            if (!Schema::hasColumn('disputes', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('safetrade_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('safetrade_transactions', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('disputes', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('safetrade_transactions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
