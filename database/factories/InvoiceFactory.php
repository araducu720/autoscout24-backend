<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\SafetradeTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        $amount = fake()->randomFloat(2, 1000, 50000);
        $fee = round($amount * 0.025, 2);

        return [
            'safetrade_transaction_id' => SafetradeTransaction::factory(),
            'invoice_number' => 'INV-' . fake()->unique()->numerify('######'),
            'issue_date' => now(),
            'due_date' => now()->addDays(14),
            'amount' => $amount,
            'escrow_fee' => $fee,
            'total' => $amount + $fee,
            'status' => fake()->randomElement(['draft', 'issued', 'paid', 'overdue']),
        ];
    }

    public function paid(): self
    {
        return $this->state(fn () => ['status' => 'paid']);
    }
}
