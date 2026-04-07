<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disputes', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('transaction_id')->constrained('safetrade_transactions')->onDelete('cascade');
            $table->foreignId('opened_by')->constrained('users')->onDelete('cascade');
            
            // Dispute type
            $table->enum('type', [
                'payment_not_received',      // Seller didn't receive payment
                'payment_amount_incorrect',  // Wrong amount transferred
                'vehicle_not_as_described',  // Vehicle condition issues
                'vehicle_not_delivered',     // Vehicle not handed over
                'documentation_issues',      // Missing or incorrect documents
                'damage_during_handover',    // Vehicle damaged during pickup
                'fraud',                     // Suspected fraud
                'other',                     // Other issues
            ]);
            
            // Description
            $table->text('description');
            
            // Status workflow
            $table->enum('status', [
                'open',           // Just opened
                'under_review',   // Admin is reviewing
                'awaiting_info',  // Waiting for additional info from parties
                'mediation',      // In mediation process
                'resolved',       // Resolved successfully
                'closed',         // Closed without resolution
                'escalated',      // Escalated to legal/higher authority
            ])->default('open');
            
            // Priority
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            
            // Admin handling
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            $table->timestamp('assigned_at')->nullable();
            
            // Resolution
            $table->text('resolution_notes')->nullable();
            $table->enum('resolution_outcome', [
                'in_favor_seller',
                'in_favor_dealer',
                'mutual_agreement',
                'refund_issued',
                'no_action_required',
                'escalated_to_legal',
            ])->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users');
            $table->timestamp('resolved_at')->nullable();
            
            // Evidence/attachments stored separately in dispute_attachments
            
            // Timeline tracking
            $table->timestamp('last_activity_at')->nullable();
            
            $table->timestamps();
            
            $table->index('status');
            $table->index('priority');
            $table->index('type');
        });

        // Dispute attachments (evidence)
        Schema::create('dispute_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dispute_id')->constrained()->onDelete('cascade');
            $table->foreignId('uploaded_by')->constrained('users');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('mime_type');
            $table->unsignedInteger('file_size');
            $table->timestamps();
        });

        // Dispute messages (communication thread)
        Schema::create('dispute_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dispute_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users');
            $table->text('message');
            $table->boolean('is_internal')->default(false); // Admin-only notes
            $table->boolean('is_system')->default(false);   // System-generated
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispute_messages');
        Schema::dropIfExists('dispute_attachments');
        Schema::dropIfExists('disputes');
    }
};
