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
        Schema::table('users', function (Blueprint $table) {
            // Bank details for SafeTrade payments
            $table->string('bank_name')->nullable()->after('phone');
            $table->string('iban', 34)->nullable()->after('bank_name');
            $table->string('bic', 11)->nullable()->after('iban');
            $table->string('account_holder')->nullable()->after('bic');
            
            // User preferences
            $table->string('locale', 5)->default('en')->after('account_holder');
            $table->string('currency', 3)->default('EUR')->after('locale');
            $table->string('country', 100)->nullable()->after('currency');
            
            // Profile completeness
            $table->boolean('bank_details_verified')->default(false)->after('country');
            $table->timestamp('bank_details_verified_at')->nullable()->after('bank_details_verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'bank_name',
                'iban',
                'bic',
                'account_holder',
                'locale',
                'currency',
                'country',
                'bank_details_verified',
                'bank_details_verified_at',
            ]);
        });
    }
};
