<?php

namespace Database\Factories;

use App\Models\Dealer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DealerFactory extends Factory
{
    protected $model = Dealer::class;

    public function definition(): array
    {
        $companyTypes = ['GmbH', 'AG', 'OHG', 'KG', 'UG', 'e.K.'];
        $companyName = fake()->company() . ' ' . fake()->randomElement($companyTypes);

        return [
            'user_id' => User::factory(),
            'company_name' => $companyName,
            'slug' => \Str::slug($companyName) . '-' . fake()->unique()->randomNumber(5),
            'registration_number' => strtoupper(fake()->regexify('HRB[0-9]{6}')),
            'tax_id' => 'DE' . fake()->numerify('#########'),
            'website' => fake()->optional(0.7)->url(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->companyEmail(),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'postal_code' => fake()->postcode(),
            'country' => fake()->randomElement(['Germany', 'Austria', 'Switzerland']),
            'description' => fake()->paragraph(2),
            'type' => fake()->randomElement(['independent', 'franchise', 'manufacturer']),
            'logo' => null,
            'is_verified' => false,
            'is_active' => true,
            'verified_at' => null,
            'rating' => null,
            'total_reviews' => 0,
            'total_purchases' => 0,
        ];
    }

    public function verified(): self
    {
        return $this->state(fn () => [
            'is_verified' => true,
            'verified_at' => now(),
        ]);
    }

    public function withRating(): self
    {
        $reviews = fake()->numberBetween(5, 100);
        return $this->state(fn () => [
            'rating' => fake()->randomFloat(1, 3.0, 5.0),
            'total_reviews' => $reviews,
            'total_transactions' => fake()->numberBetween($reviews, $reviews * 2),
        ]);
    }

    public function active(): self
    {
        return $this->verified()->withRating();
    }
}
