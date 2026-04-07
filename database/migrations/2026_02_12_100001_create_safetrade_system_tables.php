<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ──────────────────────────────────────────────
        // ORDERS — buyer places an order on a vehicle
        // ──────────────────────────────────────────────
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->decimal('total_price', 12, 2);
            $table->decimal('escrow_fee', 10, 2)->default(0);
            $table->enum('status', [
                'pending',
                'accepted',
                'rejected',
                'completed',
                'cancelled',
            ])->default('pending');
            $table->enum('delivery_method', ['pickup', 'delivery', 'shipping'])->default('pickup');
            $table->string('delivery_address')->nullable();
            $table->text('message')->nullable();
            $table->timestamp('payment_deadline')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('order_number');
        });

        // ──────────────────────────────────────────────
        // SAFETRADE TRANSACTIONS — links order to payment flow
        // ──────────────────────────────────────────────
        Schema::create('safetrade_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');

            // Vehicle snapshot (in case listing is later changed)
            $table->string('vehicle_title');
            $table->decimal('vehicle_price', 12, 2);

            // Payment
            $table->enum('payment_method', ['bank_transfer', 'credit_card', 'escrow', 'cash'])->default('bank_transfer');
            $table->enum('payment_status', ['pending', 'processing', 'completed', 'failed', 'refunded'])->default('pending');
            $table->decimal('amount', 12, 2);
            $table->decimal('escrow_fee', 10, 2)->default(0);

            // Status
            $table->enum('status', [
                'pending',          // Created, awaiting buyer action
                'confirmed',        // Buyer confirmed order
                'in_transit',       // Vehicle being delivered
                'delivered',        // Vehicle received by buyer
                'completed',        // Both parties confirmed
                'cancelled',        // Cancelled
                'disputed',         // Under dispute
            ])->default('pending');

            // Escrow
            $table->enum('escrow_status', [
                'pending',          // Awaiting buyer funding
                'funded',           // Buyer funded escrow
                'disputed',         // Dispute opened
                'released',         // Funds released to seller
                'refunded',         // Refunded to buyer
            ])->default('pending');

            // Delivery
            $table->enum('delivery_method', ['pickup', 'delivery', 'shipping'])->default('pickup');
            $table->string('delivery_address')->nullable();
            $table->string('tracking_number')->nullable();

            // Notes
            $table->text('notes')->nullable();
            $table->text('cancellation_reason')->nullable();

            // Timestamps
            $table->timestamp('funded_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('escrow_status');
            $table->index('reference');
        });

        // ──────────────────────────────────────────────
        // ESCROW ACCOUNTS — holds funds between parties
        // ──────────────────────────────────────────────
        Schema::create('escrow_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('safetrade_transaction_id')->constrained('safetrade_transactions')->onDelete('cascade');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->enum('status', [
                'pending',
                'funded',
                'disputed',
                'released',
                'refunded',
            ])->default('pending');
            $table->json('release_conditions')->nullable();
            $table->string('dispute_reason')->nullable();
            $table->json('dispute_evidence')->nullable();
            $table->string('payment_reference')->nullable();
            $table->string('escrow_iban')->nullable();
            $table->timestamp('funded_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();

            $table->index('status');
        });

        // ──────────────────────────────────────────────
        // INVOICES — generated per transaction
        // ──────────────────────────────────────────────
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('safetrade_transaction_id')->constrained('safetrade_transactions')->onDelete('cascade');
            $table->string('invoice_number')->unique();
            $table->date('issue_date');
            $table->date('due_date')->nullable();
            $table->decimal('amount', 12, 2);
            $table->decimal('escrow_fee', 10, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->enum('status', ['draft', 'issued', 'paid', 'overdue', 'cancelled'])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('invoice_number');
            $table->index('status');
        });

        // ──────────────────────────────────────────────
        // TRANSACTION TIMELINE — audit trail
        // ──────────────────────────────────────────────
        Schema::create('transaction_timelines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('safetrade_transaction_id')->constrained('safetrade_transactions')->onDelete('cascade');
            $table->string('event');
            $table->text('description');
            $table->foreignId('actor_id')->constrained('users')->onDelete('cascade');
            $table->string('actor_name');
            $table->string('actor_role')->default('buyer'); // buyer, seller, admin, mediator
            $table->json('metadata')->nullable();
            $table->timestamp('timestamp');
            $table->timestamps();

            $table->index('safetrade_transaction_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_timelines');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('escrow_transactions');
        Schema::dropIfExists('safetrade_transactions');
        Schema::dropIfExists('orders');
    }
};
