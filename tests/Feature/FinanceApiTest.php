<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\FinanceCalculatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_calculate_monthly_payment(): void
    {
        $response = $this->postJson('/api/v1/finance/calculate', [
            'vehicle_price' => 25000,
            'down_payment' => 5000,
            'annual_interest_rate' => 4.9,
            'term_months' => 48,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [
                'monthly_payment',
                'total_payment',
                'total_interest',
                'loan_amount',
                'down_payment',
                'term_months',
                'annual_rate',
            ]]);

        $data = $response->json('data');
        $this->assertEquals(20000, $data['loan_amount']);
        $this->assertGreaterThan(0, $data['monthly_payment']);
        $this->assertGreaterThan(0, $data['total_interest']);
    }

    public function test_can_compare_financing_options(): void
    {
        $response = $this->postJson('/api/v1/finance/compare-options', [
            'vehicle_price' => 30000,
            'down_payment' => 6000,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);

        $options = $response->json('data');
        $this->assertGreaterThanOrEqual(4, count($options));

        // Verify shorter terms have higher monthly payments
        $this->assertGreaterThan(
            $options[count($options) - 1]['monthly_payment'],
            $options[0]['monthly_payment']
        );
    }

    public function test_can_get_currencies(): void
    {
        $response = $this->getJson('/api/v1/finance/currencies');

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);

        $currencies = $response->json('data');
        $this->assertArrayHasKey('EUR', $currencies);
        $this->assertArrayHasKey('GBP', $currencies);
        $this->assertArrayHasKey('CHF', $currencies);
    }

    public function test_can_convert_currency(): void
    {
        $response = $this->postJson('/api/v1/finance/convert', [
            'amount' => 25000,
            'from' => 'EUR',
            'to' => 'GBP',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [
                'original_amount',
                'original_currency',
                'converted_amount',
                'target_currency',
                'exchange_rate',
                'formatted',
            ]]);

        $data = $response->json('data');
        $this->assertEquals(25000, $data['original_amount']);
        $this->assertGreaterThan(0, $data['converted_amount']);
    }

    public function test_finance_calculation_validation(): void
    {
        $response = $this->postJson('/api/v1/finance/calculate', [
            'vehicle_price' => 0,
            'down_payment' => -100,
            'annual_interest_rate' => 50,
            'term_months' => 200,
        ]);

        $response->assertStatus(422);
    }

    public function test_zero_interest_calculation(): void
    {
        $service = new FinanceCalculatorService();
        $result = $service->calculateMonthlyPayment(24000, 0, 0, 24);

        $this->assertEquals(1000, $result['monthly_payment']);
        $this->assertEquals(0, $result['total_interest']);
    }

    public function test_full_down_payment(): void
    {
        $service = new FinanceCalculatorService();
        $result = $service->calculateMonthlyPayment(25000, 25000, 5, 48);

        $this->assertEquals(0, $result['monthly_payment']);
        $this->assertEquals(0, $result['loan_amount']);
    }

    public function test_exchange_rates_endpoint(): void
    {
        $response = $this->getJson('/api/v1/finance/exchange-rates?base=EUR');

        $response->assertStatus(200)
            ->assertJsonStructure(['base', 'rates']);
    }
}
