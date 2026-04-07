<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->text('admin_reply')->nullable()->after('message');
            $table->string('reply_subject')->nullable()->after('admin_reply');
            $table->timestamp('replied_at')->nullable()->after('reply_subject');
            $table->foreignId('replied_by')->nullable()->after('replied_at')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropForeign(['replied_by']);
            $table->dropColumn(['admin_reply', 'reply_subject', 'replied_at', 'replied_by']);
        });
    }
};
