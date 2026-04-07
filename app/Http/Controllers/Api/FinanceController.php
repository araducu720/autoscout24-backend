<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FinanceCalculatorService;
use App\Services\CurrencyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function __construct(
        private FinanceCalculatorService $calculator,
        private CurrencyService $currencyService,
    ) {}

    public function calculate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'vehicle_price' => 'required|numeric|min:100',
            'down_payment' => 'required|numeric|min:0',
            'annual_interest_rate' => 'required|numeric|min:0|max:30',
            'term_months' => 'required|integer|min:6|max:120',
        ]);

        $result = $this->calculator->calculateMonthlyPayment(
            $validated['vehicle_price'],
            $validated['down_payment'],
            $validated['annual_interest_rate'],
            $validated['term_months']
        );

        return response()->json(['data' => $result]);
    }

    public function compareOptions(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'vehicle_price' => 'required|numeric|min:100',
            'down_payment' => 'numeric|min:0',
        ]);

        $options = $this->calculator->compareFinancingOptions(
            $validated['vehicle_price'],
            $validated['down_payment'] ?? 0
        );

        return response()->json(['data' => $options]);
    }

    public function currencies(): JsonResponse
    {
        return response()->json([
            'data' => $this->currencyService->getSupportedCurrencies(),
        ]);
    }

    public function exchangeRates(Request $request): JsonResponse
    {
        $base = $request->query('base', 'EUR');
        $rates = $this->currencyService->getExchangeRates($base);

        return response()->json([
            'base' => $base,
            'rates' => $rates,
            'updated_at' => now()->toISOString(),
        ]);
    }

    public function convert(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'from' => 'required|string|size:3',
            'to' => 'required|string|size:3',
        ]);

        $result = $this->currencyService->convert(
            $validated['amount'],
            strtoupper($validated['from']),
            strtoupper($validated['to'])
        );

        return response()->json(['data' => $result]);
    }
}
