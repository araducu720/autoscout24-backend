<?php

namespace Database\Factories;

use App\Models\ContactMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactMessageFactory extends Factory
{
    protected $model = ContactMessage::class;

    public function definition(): array
    {
        return [
            'vehicle_id' => null,
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'message' => fake()->paragraph(2),
            'status' => fake()->randomElement(['new', 'read', 'replied']),
        ];
    }

    public function replied(): self
    {
        return $this->state(fn () => [
            'status' => 'replied',
            'admin_reply' => fake()->paragraph(),
            'reply_subject' => 'Re: ' . fake()->sentence(),
            'replied_at' => now(),
        ]);
    }
}
