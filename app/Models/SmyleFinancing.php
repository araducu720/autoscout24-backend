<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmyleFinancing extends Model
{
    protected $table = 'smyle_financing';

    protected $fillable = [
        'smyle_order_id', 'buyer_id', 'status', 'application_reference', 'bank_name',
        'vehicle_price', 'down_payment', 'loan_amount', 'interest_rate',
        'effective_rate', 'monthly_payment', 'loan_term_months', 'total_cost',
        'final_payment', 'employment_status', 'monthly_income', 'monthly_expenses',
        'rejection_reason', 'notes',
        'submitted_at', 'approved_at', 'contract_signed_at',
    ];

    protected $casts = [
        'vehicle_price' => 'decimal:2',
        'down_payment' => 'decimal:2',
        'loan_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'effective_rate' => 'decimal:2',
        'monthly_payment' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'final_payment' => 'decimal:2',
        'monthly_income' => 'decimal:2',
        'monthly_expenses' => 'decimal:2',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'contract_signed_at' => 'datetime',
    ];

    public function smyleOrder(): BelongsTo
    {
        return $this->belongsTo(SmyleOrder::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    // Calculate monthly payment
    public static function calculateMonthlyPayment(float $loanAmount, float $interestRate, int $termMonths): float
    {
        if ($interestRate <= 0) {
            return round($loanAmount / $termMonths, 2);
        }

        $monthlyRate = $interestRate / 100 / 12;
        $payment = $loanAmount * ($monthlyRate * pow(1 + $monthlyRate, $termMonths)) / (pow(1 + $monthlyRate, $termMonths) - 1);

        return round($payment, 2);
    }
}
