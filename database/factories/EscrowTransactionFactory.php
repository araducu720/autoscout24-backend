<?php

namespace Database\Factories;

use App\Models\EscrowTransaction;
use App\Models\SafetradeTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EscrowTransactionFactory extends Factory
{
    protected $model = EscrowTransaction::class;

    public function definition(): array
    {
        return [
            'safetrade_transaction_id' => SafetradeTransaction::factory(),
            'buyer_id' => User::factory(),
            'seller_id' => User::factory(),
            'amount' => fake()->randomFloat(2, 1000, 50000),
            'status' => fake()->randomElement(['pending', 'funded', 'held', 'released', 'refunded', 'disputed']),
            'release_conditions' => ['vehicle_delivered', 'buyer_approved'],
            'payment_reference' => 'ESC-' . strtoupper(fake()->bothify('????-####')),
            'escrow_iban' => fake()->iban(),
        ];
    }

    public function funded(): self
    {
        return $this->state(fn () => [
            'status' => 'funded',
            'funded_at' => now(),
        ]);
    }

    public function released(): self
    {
        return $this->state(fn () => [
            'status' => 'released',
            'funded_at' => now()->subDays(7),
            'released_at' => now(),
        ]);
    }
}
