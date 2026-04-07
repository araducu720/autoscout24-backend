<?php

namespace Database\Factories;

use App\Models\SafetradeTransaction;
use App\Models\Order;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

class SafetradeTransactionFactory extends Factory
{
    protected $model = SafetradeTransaction::class;

    public function definition(): array
    {
        $price = fake()->randomFloat(2, 1000, 50000);
        $fee = round($price * 0.025, 2);

        return [
            'reference' => 'AS24-ST-' . strtoupper(fake()->bothify('????-####')),
            'order_id' => Order::factory(),
            'buyer_id' => User::factory(),
            'seller_id' => User::factory(),
            'vehicle_id' => Vehicle::factory(),
            'vehicle_title' => fake()->sentence(3),
            'vehicle_price' => $price,
            'payment_method' => fake()->randomElement(['bank_transfer', 'escrow']),
            'payment_status' => fake()->randomElement(['pending', 'processing', 'completed', 'failed', 'refunded']),
            'amount' => $price + $fee,
            'escrow_fee' => $fee,
            'status' => fake()->randomElement(['pending', 'confirmed', 'in_transit', 'delivered', 'completed', 'cancelled', 'disputed']),
            'escrow_status' => fake()->randomElement(['pending', 'funded', 'held', 'released', 'refunded', 'disputed']),
            'delivery_method' => fake()->randomElement(['pickup', 'shipping']),
        ];
    }

    public function completed(): self
    {
        return $this->state(fn () => [
            'status' => 'completed',
            'payment_status' => 'completed',
            'escrow_status' => 'released',
            'confirmed_at' => now()->subDays(10),
            'delivered_at' => now()->subDays(3),
            'completed_at' => now(),
        ]);
    }

    public function pending(): self
    {
        return $this->state(fn () => [
            'status' => 'pending',
            'payment_status' => 'pending',
            'escrow_status' => 'pending',
        ]);
    }
}
