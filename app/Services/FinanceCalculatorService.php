<?php

namespace App\Services;

class FinanceCalculatorService
{
    public function calculateMonthlyPayment(
        float $vehiclePrice,
        float $downPayment,
        float $annualInterestRate,
        int $termMonths
    ): array {
        $loanAmount = $vehiclePrice - $downPayment;

        if ($loanAmount <= 0) {
            return [
                'monthly_payment' => 0,
                'total_payment' => $downPayment,
                'total_interest' => 0,
                'loan_amount' => 0,
                'down_payment' => $downPayment,
                'term_months' => $termMonths,
                'annual_rate' => $annualInterestRate,
            ];
        }

        $monthlyRate = $annualInterestRate / 100 / 12;

        if ($monthlyRate === 0.0) {
            $monthlyPayment = $loanAmount / $termMonths;
        } else {
            $monthlyPayment = $loanAmount * ($monthlyRate * pow(1 + $monthlyRate, $termMonths))
                / (pow(1 + $monthlyRate, $termMonths) - 1);
        }

        $totalPayment = ($monthlyPayment * $termMonths) + $downPayment;
        $totalInterest = $totalPayment - $vehiclePrice;

        return [
            'monthly_payment' => round($monthlyPayment, 2),
            'total_payment' => round($totalPayment, 2),
            'total_interest' => round($totalInterest, 2),
            'loan_amount' => round($loanAmount, 2),
            'down_payment' => $downPayment,
            'term_months' => $termMonths,
            'annual_rate' => $annualInterestRate,
            'amortization_schedule' => $this->generateAmortizationSchedule(
                $loanAmount, $monthlyRate, $monthlyPayment, $termMonths
            ),
        ];
    }

    public function compareFinancingOptions(float $vehiclePrice, float $downPayment): array
    {
        $options = [
            ['rate' => 3.9, 'term' => 24, 'label' => '2 years - Low rate'],
            ['rate' => 4.5, 'term' => 36, 'label' => '3 years - Standard'],
            ['rate' => 4.9, 'term' => 48, 'label' => '4 years - Medium'],
            ['rate' => 5.5, 'term' => 60, 'label' => '5 years - Extended'],
            ['rate' => 5.9, 'term' => 72, 'label' => '6 years - Long-term'],
        ];

        return array_map(function ($option) use ($vehiclePrice, $downPayment) {
            $result = $this->calculateMonthlyPayment(
                $vehiclePrice, $downPayment, $option['rate'], $option['term']
            );
            $result['label'] = $option['label'];
            unset($result['amortization_schedule']);
            return $result;
        }, $options);
    }

    private function generateAmortizationSchedule(
        float $principal,
        float $monthlyRate,
        float $monthlyPayment,
        int $termMonths
    ): array {
        $schedule = [];
        $balance = $principal;
        $maxEntries = min($termMonths, 12); // Only return first year for brevity

        for ($month = 1; $month <= $maxEntries; $month++) {
            $interestPayment = $balance * $monthlyRate;
            $principalPayment = $monthlyPayment - $interestPayment;
            $balance -= $principalPayment;

            $schedule[] = [
                'month' => $month,
                'payment' => round($monthlyPayment, 2),
                'principal' => round($principalPayment, 2),
                'interest' => round($interestPayment, 2),
                'balance' => round(max(0, $balance), 2),
            ];
        }

        return $schedule;
    }
}
