<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CurrencyService
{
    private const SUPPORTED_CURRENCIES = [
        'EUR' => ['symbol' => '€', 'name' => 'Euro'],
        'GBP' => ['symbol' => '£', 'name' => 'British Pound'],
        'CHF' => ['symbol' => 'CHF', 'name' => 'Swiss Franc'],
        'PLN' => ['symbol' => 'zł', 'name' => 'Polish Zloty'],
        'RON' => ['symbol' => 'lei', 'name' => 'Romanian Leu'],
        'CZK' => ['symbol' => 'Kč', 'name' => 'Czech Koruna'],
        'SEK' => ['symbol' => 'kr', 'name' => 'Swedish Krona'],
        'HUF' => ['symbol' => 'Ft', 'name' => 'Hungarian Forint'],
        'DKK' => ['symbol' => 'kr', 'name' => 'Danish Krone'],
        'NOK' => ['symbol' => 'kr', 'name' => 'Norwegian Krone'],
        'USD' => ['symbol' => '$', 'name' => 'US Dollar'],
    ];

    // Fallback rates when API is unavailable (EUR base)
    private const FALLBACK_RATES = [
        'EUR' => 1.0,
        'GBP' => 0.86,
        'CHF' => 0.94,
        'PLN' => 4.32,
        'RON' => 4.97,
        'CZK' => 25.30,
        'SEK' => 11.35,
        'HUF' => 396.50,
        'DKK' => 7.46,
        'NOK' => 11.60,
        'USD' => 1.08,
    ];

    public function getSupportedCurrencies(): array
    {
        return self::SUPPORTED_CURRENCIES;
    }

    public function getExchangeRates(string $baseCurrency = 'EUR'): array
    {
        return Cache::remember("exchange_rates_{$baseCurrency}", 3600, function () use ($baseCurrency) {
            try {
                // Using free API - replace with production API key
                $response = Http::timeout(5)->get(
                    "https://api.exchangerate-api.com/v4/latest/{$baseCurrency}"
                );

                if ($response->successful()) {
                    $data = $response->json();
                    $rates = [];
                    foreach (array_keys(self::SUPPORTED_CURRENCIES) as $currency) {
                        $rates[$currency] = $data['rates'][$currency] ?? self::FALLBACK_RATES[$currency] ?? 1.0;
                    }
                    return $rates;
                }
            } catch (\Exception $e) {
                // Fall through to fallback rates
            }

            return $this->getFallbackRates($baseCurrency);
        });
    }

    public function convert(float $amount, string $from, string $to): array
    {
        $rates = $this->getExchangeRates($from);
        $rate = $rates[$to] ?? 1.0;
        $converted = round($amount * $rate, 2);

        return [
            'original_amount' => $amount,
            'original_currency' => $from,
            'converted_amount' => $converted,
            'target_currency' => $to,
            'exchange_rate' => $rate,
            'symbol' => self::SUPPORTED_CURRENCIES[$to]['symbol'] ?? $to,
            'formatted' => $this->formatCurrency($converted, $to),
        ];
    }

    public function formatCurrency(float $amount, string $currency): string
    {
        $info = self::SUPPORTED_CURRENCIES[$currency] ?? null;
        if (!$info) {
            return number_format($amount, 2) . ' ' . $currency;
        }

        return match ($currency) {
            'EUR' => '€' . number_format($amount, 2, ',', '.'),
            'GBP' => '£' . number_format($amount, 2, '.', ','),
            'USD' => '$' . number_format($amount, 2, '.', ','),
            'CHF' => 'CHF ' . number_format($amount, 2, '.', "'"),
            'PLN' => number_format($amount, 2, ',', ' ') . ' zł',
            'RON' => number_format($amount, 2, ',', '.') . ' lei',
            'CZK' => number_format($amount, 0, ',', ' ') . ' Kč',
            'SEK' => number_format($amount, 0, ' ', ' ') . ' kr',
            'HUF' => number_format($amount, 0, ',', ' ') . ' Ft',
            default => number_format($amount, 2) . ' ' . $currency,
        };
    }

    private function getFallbackRates(string $baseCurrency): array
    {
        $eurRate = self::FALLBACK_RATES[$baseCurrency] ?? 1.0;
        $rates = [];

        foreach (self::FALLBACK_RATES as $currency => $rateToEur) {
            $rates[$currency] = round($rateToEur / $eurRate, 6);
        }

        return $rates;
    }
}
