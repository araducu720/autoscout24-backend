<?php

namespace Database\Factories;

use App\Models\Dispute;
use App\Models\SafetradeTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DisputeFactory extends Factory
{
    protected $model = Dispute::class;

    public function definition(): array
    {
        return [
            'reference' => 'DSP-' . strtoupper(fake()->bothify('????-####')),
            'transaction_id' => SafetradeTransaction::factory(),
            'opened_by' => User::factory(),
            'type' => fake()->randomElement([
                'payment_not_received', 'payment_amount_incorrect',
                'vehicle_not_as_described', 'vehicle_not_delivered',
                'documentation_issues', 'damage_during_handover', 'fraud', 'other',
            ]),
            'description' => fake()->paragraph(2),
            'status' => fake()->randomElement(['open', 'under_review', 'awaiting_info', 'mediation', 'resolved', 'closed', 'escalated']),
            'priority' => fake()->randomElement(['low', 'medium', 'high', 'urgent']),
            'buyer_accepted_resolution' => false,
            'seller_accepted_resolution' => false,
        ];
    }

    public function resolved(): self
    {
        return $this->state(fn () => [
            'status' => 'resolved',
            'resolved_by' => User::factory(),
            'resolved_at' => now(),
            'resolution_outcome' => fake()->randomElement([
                'in_favor_seller', 'in_favor_dealer', 'mutual_agreement',
                'refund_issued', 'no_action_required', 'escalated_to_legal',
            ]),
            'resolution_notes' => fake()->paragraph(),
        ]);
    }
}
