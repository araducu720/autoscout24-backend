<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE safetrade_transactions MODIFY COLUMN payment_status ENUM('pending','processing','completed','failed','refunded','awaiting_verification') NOT NULL DEFAULT 'pending'");
        DB::statement("ALTER TABLE safetrade_transactions MODIFY COLUMN status ENUM('pending','confirmed','in_transit','delivered','completed','cancelled','disputed','payment_uploaded') NOT NULL DEFAULT 'pending'");
        DB::statement("ALTER TABLE safetrade_transactions MODIFY COLUMN escrow_status ENUM('pending','funded','disputed','released','refunded','awaiting_verification') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE safetrade_transactions MODIFY COLUMN payment_status ENUM('pending','processing','completed','failed','refunded') NOT NULL DEFAULT 'pending'");
        DB::statement("ALTER TABLE safetrade_transactions MODIFY COLUMN status ENUM('pending','confirmed','in_transit','delivered','completed','cancelled','disputed') NOT NULL DEFAULT 'pending'");
        DB::statement("ALTER TABLE safetrade_transactions MODIFY COLUMN escrow_status ENUM('pending','funded','disputed','released','refunded') NOT NULL DEFAULT 'pending'");
    }
};
