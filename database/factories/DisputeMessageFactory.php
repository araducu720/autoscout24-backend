<?php

namespace Database\Factories;

use App\Models\DisputeMessage;
use App\Models\Dispute;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DisputeMessageFactory extends Factory
{
    protected $model = DisputeMessage::class;

    public function definition(): array
    {
        return [
            'dispute_id' => Dispute::factory(),
            'user_id' => User::factory(),
            'message' => fake()->paragraph(),
            'is_internal' => false,
            'is_system' => false,
        ];
    }

    public function internal(): self
    {
        return $this->state(fn () => ['is_internal' => true]);
    }

    public function system(): self
    {
        return $this->state(fn () => ['is_system' => true, 'user_id' => null]);
    }
}
