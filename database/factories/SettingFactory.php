<?php

namespace Database\Factories;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Factories\Factory;

class SettingFactory extends Factory
{
    protected $model = Setting::class;

    public function definition(): array
    {
        return [
            'group' => fake()->randomElement(['general', 'safetrade', 'email', 'seo']),
            'key' => fake()->unique()->slug(2),
            'value' => fake()->word(),
            'type' => fake()->randomElement(['text', 'boolean', 'number', 'select']),
            'label' => fake()->sentence(3),
            'description' => fake()->sentence(),
            'is_public' => fake()->boolean(),
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }
}
