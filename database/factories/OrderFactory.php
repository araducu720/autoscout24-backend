<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $price = fake()->randomFloat(2, 1000, 50000);
        $fee = round($price * 0.025, 2);

        return [
            'order_number' => 'ORD-' . strtoupper(fake()->bothify('????-####')),
            'buyer_id' => User::factory(),
            'seller_id' => User::factory(),
            'vehicle_id' => Vehicle::factory(),
            'total_price' => $price,
            'escrow_fee' => $fee,
            'status' => fake()->randomElement(['pending', 'accepted', 'rejected', 'completed', 'cancelled']),
            'delivery_method' => fake()->randomElement(['pickup', 'shipping']),
            'delivery_address' => fake()->optional()->address(),
            'message' => fake()->optional()->sentence(),
            'payment_deadline' => now()->addDays(7),
        ];
    }

    public function accepted(): self
    {
        return $this->state(fn () => [
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);
    }

    public function rejected(): self
    {
        return $this->state(fn () => [
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejection_reason' => fake()->sentence(),
        ]);
    }
}
