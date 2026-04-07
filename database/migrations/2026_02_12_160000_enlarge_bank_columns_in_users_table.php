<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Enlarge iban and bic columns to hold encrypted (base64) values.
     * Original sizes (34/11) are for plain-text IBAN/BIC,
     * but the 'encrypted' cast produces ~192 chars of ciphertext.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('iban')->nullable()->change();
            $table->text('bic')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('iban', 34)->nullable()->change();
            $table->string('bic', 11)->nullable()->change();
        });
    }
};
