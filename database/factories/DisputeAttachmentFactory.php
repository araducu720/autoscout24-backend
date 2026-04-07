<?php

namespace Database\Factories;

use App\Models\DisputeAttachment;
use App\Models\Dispute;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DisputeAttachmentFactory extends Factory
{
    protected $model = DisputeAttachment::class;

    public function definition(): array
    {
        return [
            'dispute_id' => Dispute::factory(),
            'uploaded_by' => User::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->sentence(),
            'file_path' => 'disputes/' . fake()->uuid() . '.pdf',
            'file_name' => fake()->word() . '.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => fake()->numberBetween(10000, 5000000),
        ];
    }
}
