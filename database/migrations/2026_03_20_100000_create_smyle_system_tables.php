<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Smyle Vehicles - vehicles eligible for the Smyle online purchase program
        Schema::create('smyle_vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->boolean('is_eligible')->default(true);
            $table->boolean('is_active')->default(true);
            $table->boolean('quality_checked')->default(false);
            $table->decimal('delivery_base_price', 10, 2)->default(599.00);
            $table->string('location_postal_code', 10)->nullable();
            $table->string('location_city')->nullable();
            $table->text('smyle_highlights')->nullable();
            $table->json('included_services')->nullable();
            $table->timestamp('listed_at')->nullable();
            $table->timestamp('delisted_at')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique('vehicle_id');
            $table->index('is_eligible');
            $table->index('is_active');
            $table->index('quality_checked');
            $table->index('location_postal_code');
        });

        // Smyle Quality Checks - expert inspection records
        Schema::create('smyle_quality_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('smyle_vehicle_id')->constrained('smyle_vehicles')->onDelete('cascade');
            $table->foreignId('inspector_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('reference')->unique();
            $table->enum('status', ['pending', 'in_progress', 'passed', 'failed', 'conditional'])->default('pending');
            $table->integer('overall_score')->nullable();
            $table->json('exterior_check')->nullable();
            $table->json('interior_check')->nullable();
            $table->json('engine_check')->nullable();
            $table->json('electronics_check')->nullable();
            $table->json('tires_brakes_check')->nullable();
            $table->json('documents_check')->nullable();
            $table->text('inspector_notes')->nullable();
            $table->json('issues_found')->nullable();
            $table->json('photos')->nullable();
            $table->boolean('roadworthy')->default(false);
            $table->date('inspection_date')->nullable();
            $table->date('valid_until')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('reference');
        });

        // Smyle Orders - online purchase orders
        Schema::create('smyle_orders', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('smyle_vehicle_id')->constrained('smyle_vehicles')->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');

            // Vehicle snapshot
            $table->string('vehicle_title');
            $table->decimal('vehicle_price', 12, 2);

            // Pricing
            $table->decimal('delivery_cost', 10, 2)->default(599.00);
            $table->decimal('service_fee', 10, 2)->default(0);
            $table->decimal('registration_fee', 10, 2)->default(0);
            $table->decimal('total_amount', 12, 2);
            $table->decimal('deposit_amount', 10, 2)->default(199.00);
            $table->decimal('remaining_amount', 12, 2);

            // Status workflow
            $table->enum('status', [
                'pending',           // Order created, awaiting deposit
                'deposit_paid',      // €199 deposit received
                'quality_check',     // Vehicle being inspected
                'registration',      // Vehicle being registered
                'insurance_active',  // Insurance activated
                'ready_for_delivery',// Everything ready
                'in_transit',        // Vehicle being delivered
                'delivered',         // Vehicle arrived at buyer
                'completed',         // All done, buyer satisfied
                'cancelled',         // Order cancelled
                'returned',          // 14-day return exercised
            ])->default('pending');

            // Payment
            $table->enum('payment_method', ['paypal', 'instant_transfer', 'financing', 'bank_transfer'])->default('bank_transfer');
            $table->enum('payment_status', ['pending', 'deposit_paid', 'fully_paid', 'refunded', 'partial_refund'])->default('pending');

            // Delivery
            $table->string('delivery_postal_code', 10);
            $table->string('delivery_city');
            $table->string('delivery_street');
            $table->string('delivery_house_number', 20)->nullable();
            $table->date('preferred_delivery_date')->nullable();
            $table->date('estimated_delivery_date')->nullable();
            $table->date('actual_delivery_date')->nullable();

            // Registration
            $table->string('desired_license_plate')->nullable();
            $table->string('registration_district')->nullable();

            // Buyer info
            $table->string('buyer_phone')->nullable();
            $table->text('buyer_notes')->nullable();

            // Cancellation/Return
            $table->text('cancellation_reason')->nullable();
            $table->text('return_reason')->nullable();
            $table->date('return_deadline')->nullable();

            // Timestamps
            $table->timestamp('deposit_paid_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('payment_status');
            $table->index('reference');
            $table->index('buyer_id');
        });

        // Smyle Deliveries - delivery scheduling and tracking
        Schema::create('smyle_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('smyle_order_id')->constrained('smyle_orders')->onDelete('cascade');
            $table->string('tracking_number')->nullable();
            $table->enum('status', [
                'pending',
                'scheduled',
                'picked_up',
                'in_transit',
                'out_for_delivery',
                'delivered',
                'failed',
                'returned',
            ])->default('pending');

            $table->string('driver_name')->nullable();
            $table->string('driver_phone')->nullable();
            $table->string('transport_company')->nullable();

            // Origin
            $table->string('pickup_postal_code', 10)->nullable();
            $table->string('pickup_city')->nullable();
            $table->string('pickup_address')->nullable();

            // Destination
            $table->string('delivery_postal_code', 10);
            $table->string('delivery_city');
            $table->string('delivery_address');

            // Distance & cost
            $table->integer('distance_km')->nullable();
            $table->decimal('delivery_cost', 10, 2);

            // Schedule
            $table->date('scheduled_pickup_date')->nullable();
            $table->date('scheduled_delivery_date')->nullable();
            $table->string('delivery_time_slot')->nullable();
            $table->timestamp('actual_pickup_at')->nullable();
            $table->timestamp('actual_delivery_at')->nullable();

            // Handover
            $table->text('delivery_notes')->nullable();
            $table->json('handover_checklist')->nullable();
            $table->string('buyer_signature_path')->nullable();
            $table->json('delivery_photos')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('tracking_number');
        });

        // Smyle Registrations - vehicle registration handling
        Schema::create('smyle_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('smyle_order_id')->constrained('smyle_orders')->onDelete('cascade');
            $table->enum('status', [
                'pending',
                'documents_requested',
                'documents_received',
                'submitted',
                'approved',
                'plates_ordered',
                'completed',
                'failed',
            ])->default('pending');

            $table->string('desired_plate')->nullable();
            $table->string('assigned_plate')->nullable();
            $table->string('registration_district');
            $table->string('registration_number')->nullable();

            // Owner details
            $table->string('owner_full_name');
            $table->string('owner_address');
            $table->date('owner_date_of_birth')->nullable();
            $table->string('owner_id_number')->nullable();

            // Documents
            $table->json('required_documents')->nullable();
            $table->json('submitted_documents')->nullable();
            $table->boolean('documents_complete')->default(false);

            // Fees
            $table->decimal('registration_fee', 10, 2)->default(0);
            $table->decimal('plates_fee', 10, 2)->default(0);

            $table->text('notes')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('status');
        });

        // Smyle Insurances - temporary insurance records
        Schema::create('smyle_insurances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('smyle_order_id')->constrained('smyle_orders')->onDelete('cascade');
            $table->enum('type', ['liability', 'comprehensive', 'both'])->default('both');
            $table->enum('status', ['pending', 'active', 'expired', 'cancelled'])->default('pending');

            $table->string('policy_number')->nullable();
            $table->string('insurance_provider')->default('AutoScout24 Smyle');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('coverage_amount', 12, 2)->nullable();

            // Coverage details
            $table->boolean('liability_included')->default(true);
            $table->boolean('comprehensive_included')->default(true);
            $table->boolean('roadside_assistance')->default(true);
            $table->boolean('replacement_vehicle')->default(true);

            $table->text('coverage_details')->nullable();
            $table->text('exclusions')->nullable();
            $table->text('notes')->nullable();

            $table->timestamp('activated_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('policy_number');
        });

        // Smyle Warranties - 12-month warranty tracking
        Schema::create('smyle_warranties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('smyle_order_id')->constrained('smyle_orders')->onDelete('cascade');
            $table->enum('status', ['pending', 'active', 'expired', 'claimed', 'void'])->default('pending');

            $table->string('warranty_number')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('duration_months')->default(12);

            // Coverage
            $table->boolean('engine_covered')->default(true);
            $table->boolean('transmission_covered')->default(true);
            $table->boolean('electrical_covered')->default(true);
            $table->boolean('suspension_covered')->default(true);
            $table->boolean('brakes_covered')->default(true);
            $table->boolean('ac_covered')->default(true);
            $table->decimal('max_claim_amount', 12, 2)->nullable();
            $table->decimal('deductible', 10, 2)->default(0);

            // Claims
            $table->integer('claims_count')->default(0);
            $table->decimal('claims_total', 12, 2)->default(0);
            $table->json('claims_history')->nullable();

            // Roadside assistance
            $table->boolean('roadside_assistance')->default(true);
            $table->boolean('towing_included')->default(true);
            $table->boolean('replacement_mobility')->default(true);

            $table->text('terms')->nullable();
            $table->text('notes')->nullable();

            $table->timestamp('activated_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('warranty_number');
        });

        // Smyle Financing - Santander bank financing
        Schema::create('smyle_financing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('smyle_order_id')->constrained('smyle_orders')->onDelete('cascade');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', [
                'draft',
                'submitted',
                'under_review',
                'approved',
                'rejected',
                'contract_signed',
                'active',
                'completed',
                'cancelled',
            ])->default('draft');

            $table->string('application_reference')->nullable();
            $table->string('bank_name')->default('Santander Consumer Bank');

            // Loan details
            $table->decimal('vehicle_price', 12, 2);
            $table->decimal('down_payment', 12, 2)->default(0);
            $table->decimal('loan_amount', 12, 2);
            $table->decimal('interest_rate', 5, 2)->nullable();
            $table->decimal('effective_rate', 5, 2)->nullable();
            $table->decimal('monthly_payment', 10, 2)->nullable();
            $table->integer('loan_term_months')->default(48);
            $table->decimal('total_cost', 12, 2)->nullable();
            $table->decimal('final_payment', 12, 2)->nullable();

            // Applicant info
            $table->enum('employment_status', [
                'employed', 'self_employed', 'civil_servant', 'retired', 'student', 'unemployed'
            ])->nullable();
            $table->decimal('monthly_income', 10, 2)->nullable();
            $table->decimal('monthly_expenses', 10, 2)->nullable();

            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();

            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('contract_signed_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('application_reference');
        });

        // Smyle Order Timeline - audit trail
        Schema::create('smyle_order_timeline', function (Blueprint $table) {
            $table->id();
            $table->foreignId('smyle_order_id')->constrained('smyle_orders')->onDelete('cascade');
            $table->string('event');
            $table->text('description');
            $table->foreignId('actor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('actor_name');
            $table->string('actor_role')->default('system');
            $table->json('metadata')->nullable();
            $table->timestamp('timestamp');
            $table->timestamps();

            $table->index('smyle_order_id');
            $table->index('event');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('smyle_order_timeline');
        Schema::dropIfExists('smyle_financing');
        Schema::dropIfExists('smyle_warranties');
        Schema::dropIfExists('smyle_insurances');
        Schema::dropIfExists('smyle_registrations');
        Schema::dropIfExists('smyle_deliveries');
        Schema::dropIfExists('smyle_orders');
        Schema::dropIfExists('smyle_quality_checks');
        Schema::dropIfExists('smyle_vehicles');
    }
};
