<?php

namespace Database\Factories;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuditLogFactory extends Factory
{
    protected $model = AuditLog::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'action' => fake()->randomElement(['create', 'update', 'delete', 'login', 'logout']),
            'auditable_type' => fake()->randomElement(['App\\Models\\Vehicle', 'App\\Models\\User', 'App\\Models\\Order']),
            'auditable_id' => fake()->numberBetween(1, 1000),
            'old_values' => null,
            'new_values' => ['status' => 'active'],
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
        ];
    }
}
