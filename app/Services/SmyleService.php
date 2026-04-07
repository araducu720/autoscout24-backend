<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Models\SmyleVehicle;
use App\Models\SmyleOrder;
use App\Models\SmyleDelivery;
use App\Models\SmyleRegistration;
use App\Models\SmyleInsurance;
use App\Models\SmyleWarranty;
use App\Models\SmyleFinancing;
use App\Models\SmyleQualityCheck;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SmyleService
{
    // Default Smyle fees
    const DEPOSIT_AMOUNT = 199.00;
    const MIN_DELIVERY_COST = 599.00;
    const REGISTRATION_FEE = 0.00;
    const SERVICE_FEE = 0.00;
    const WARRANTY_MONTHS = 12;
    const DEFAULT_INTEREST_RATE = 4.99;
    const RETURN_PERIOD_DAYS = 14;

    /**
     * Check if a vehicle is eligible for Smyle
     */
    public function isEligible(Vehicle $vehicle): array
    {
        $currentYear = (int) date('Y');
        $issues = [];

        if ($vehicle->year < ($currentYear - 6)) {
            $issues[] = 'Vehicle is older than 6 years';
        }
        if ($vehicle->mileage > 100000) {
            $issues[] = 'Vehicle has more than 100,000 km';
        }
        if ($vehicle->condition === 'damaged') {
            $issues[] = 'Vehicle is in damaged condition';
        }
        if ($vehicle->status !== 'active') {
            $issues[] = 'Vehicle is not active';
        }

        return [
            'eligible' => empty($issues),
            'issues' => $issues,
        ];
    }

    /**
     * Calculate delivery cost based on postal codes
     */
    public function calculateDeliveryCost(string $fromPostalCode, string $toPostalCode): array
    {
        $cost = SmyleDelivery::calculateCost($fromPostalCode, $toPostalCode);
        $fromRegion = (int) substr($fromPostalCode, 0, 2);
        $toRegion = (int) substr($toPostalCode, 0, 2);
        $estimatedDays = abs($fromRegion - $toRegion) <= 10 ? 21 : 35;

        return [
            'cost' => $cost,
            'currency' => 'EUR',
            'estimated_delivery_days' => $estimatedDays,
            'from_postal_code' => $fromPostalCode,
            'to_postal_code' => $toPostalCode,
        ];
    }

    /**
     * Calculate total order cost
     */
    public function calculateOrderTotal(Vehicle $vehicle, string $deliveryPostalCode): array
    {
        $smyleVehicle = SmyleVehicle::where('vehicle_id', $vehicle->id)->first();
        $fromPostalCode = $smyleVehicle->location_postal_code ?? '80331';

        $vehiclePrice = (float) $vehicle->price;
        $deliveryInfo = $this->calculateDeliveryCost($fromPostalCode, $deliveryPostalCode);
        $deliveryCost = $deliveryInfo['cost'];
        $serviceFee = self::SERVICE_FEE;
        $registrationFee = self::REGISTRATION_FEE;
        $totalAmount = $vehiclePrice + $deliveryCost + $serviceFee + $registrationFee;
        $depositAmount = self::DEPOSIT_AMOUNT;
        $remainingAmount = $totalAmount - $depositAmount;

        return [
            'vehicle_price' => $vehiclePrice,
            'delivery_cost' => $deliveryCost,
            'service_fee' => $serviceFee,
            'registration_fee' => $registrationFee,
            'total_amount' => $totalAmount,
            'deposit_amount' => $depositAmount,
            'remaining_amount' => $remainingAmount,
            'estimated_delivery_days' => $deliveryInfo['estimated_delivery_days'],
        ];
    }

    /**
     * Calculate financing options
     */
    public function calculateFinancing(float $vehiclePrice, float $downPayment, int $termMonths = 48, float $interestRate = null): array
    {
        $interestRate = $interestRate ?? self::DEFAULT_INTEREST_RATE;
        $loanAmount = $vehiclePrice - $downPayment;

        if ($loanAmount <= 0) {
            return [
                'loan_amount' => 0,
                'monthly_payment' => 0,
                'total_cost' => $vehiclePrice,
                'total_interest' => 0,
                'interest_rate' => $interestRate,
                'effective_rate' => $interestRate,
                'term_months' => $termMonths,
            ];
        }

        $monthlyPayment = SmyleFinancing::calculateMonthlyPayment($loanAmount, $interestRate, $termMonths);
        $totalCost = ($monthlyPayment * $termMonths) + $downPayment;
        $totalInterest = $totalCost - $vehiclePrice;

        return [
            'loan_amount' => round($loanAmount, 2),
            'monthly_payment' => $monthlyPayment,
            'total_cost' => round($totalCost, 2),
            'total_interest' => round($totalInterest, 2),
            'interest_rate' => $interestRate,
            'effective_rate' => round($interestRate * 1.02, 2),
            'term_months' => $termMonths,
            'down_payment' => $downPayment,
        ];
    }

    /**
     * Create a complete Smyle order with all related records
     */
    public function createOrder(User $buyer, SmyleVehicle $smyleVehicle, array $data): SmyleOrder
    {
        return DB::transaction(function () use ($buyer, $smyleVehicle, $data) {
            $vehicle = $smyleVehicle->vehicle;
            $pricing = $this->calculateOrderTotal($vehicle, $data['delivery_postal_code']);

            // Create main order
            $order = SmyleOrder::create([
                'buyer_id' => $buyer->id,
                'smyle_vehicle_id' => $smyleVehicle->id,
                'vehicle_id' => $vehicle->id,
                'vehicle_title' => $vehicle->title ?? ($vehicle->make->name . ' ' . $vehicle->model->name),
                'vehicle_price' => $pricing['vehicle_price'],
                'delivery_cost' => $pricing['delivery_cost'],
                'service_fee' => $pricing['service_fee'],
                'registration_fee' => $pricing['registration_fee'],
                'total_amount' => $pricing['total_amount'],
                'deposit_amount' => $pricing['deposit_amount'],
                'remaining_amount' => $pricing['remaining_amount'],
                'status' => 'pending',
                'payment_method' => $data['payment_method'] ?? 'bank_transfer',
                'delivery_postal_code' => $data['delivery_postal_code'],
                'delivery_city' => $data['delivery_city'],
                'delivery_street' => $data['delivery_street'],
                'delivery_house_number' => $data['delivery_house_number'] ?? null,
                'desired_license_plate' => $data['desired_license_plate'] ?? null,
                'registration_district' => $data['registration_district'] ?? null,
                'buyer_phone' => $data['buyer_phone'] ?? null,
                'buyer_notes' => $data['buyer_notes'] ?? null,
                'preferred_delivery_date' => $data['preferred_delivery_date'] ?? null,
            ]);

            // Create delivery record
            SmyleDelivery::create([
                'smyle_order_id' => $order->id,
                'status' => 'pending',
                'pickup_postal_code' => $smyleVehicle->location_postal_code,
                'pickup_city' => $smyleVehicle->location_city,
                'delivery_postal_code' => $data['delivery_postal_code'],
                'delivery_city' => $data['delivery_city'],
                'delivery_address' => $data['delivery_street'] . ' ' . ($data['delivery_house_number'] ?? ''),
                'delivery_cost' => $pricing['delivery_cost'],
            ]);

            // Create registration record
            SmyleRegistration::create([
                'smyle_order_id' => $order->id,
                'status' => 'pending',
                'desired_plate' => $data['desired_license_plate'] ?? null,
                'registration_district' => $data['registration_district'] ?? $data['delivery_city'],
                'owner_full_name' => $buyer->name,
                'owner_address' => $data['delivery_street'] . ' ' . ($data['delivery_house_number'] ?? '') . ', ' . $data['delivery_postal_code'] . ' ' . $data['delivery_city'],
                'required_documents' => ['id_card', 'proof_of_address', 'sepa_mandate'],
            ]);

            // Create insurance record
            SmyleInsurance::create([
                'smyle_order_id' => $order->id,
                'type' => 'both',
                'status' => 'pending',
                'insurance_provider' => 'AutoScout24 Smyle',
                'liability_included' => true,
                'comprehensive_included' => true,
                'roadside_assistance' => true,
                'replacement_vehicle' => true,
            ]);

            // Create warranty record
            SmyleWarranty::create([
                'smyle_order_id' => $order->id,
                'status' => 'pending',
                'duration_months' => self::WARRANTY_MONTHS,
                'engine_covered' => true,
                'transmission_covered' => true,
                'electrical_covered' => true,
                'suspension_covered' => true,
                'brakes_covered' => true,
                'ac_covered' => true,
                'roadside_assistance' => true,
                'towing_included' => true,
                'replacement_mobility' => true,
            ]);

            // Create financing record if requested
            if (($data['payment_method'] ?? '') === 'financing') {
                $finCalc = $this->calculateFinancing(
                    $pricing['vehicle_price'],
                    $data['down_payment'] ?? 0,
                    $data['loan_term_months'] ?? 48
                );

                SmyleFinancing::create([
                    'smyle_order_id' => $order->id,
                    'buyer_id' => $buyer->id,
                    'status' => 'draft',
                    'bank_name' => 'Santander Consumer Bank',
                    'vehicle_price' => $pricing['vehicle_price'],
                    'down_payment' => $data['down_payment'] ?? 0,
                    'loan_amount' => $finCalc['loan_amount'],
                    'interest_rate' => $finCalc['interest_rate'],
                    'effective_rate' => $finCalc['effective_rate'],
                    'monthly_payment' => $finCalc['monthly_payment'],
                    'loan_term_months' => $data['loan_term_months'] ?? 48,
                    'total_cost' => $finCalc['total_cost'],
                    'employment_status' => $data['employment_status'] ?? null,
                    'monthly_income' => $data['monthly_income'] ?? null,
                    'monthly_expenses' => $data['monthly_expenses'] ?? null,
                ]);
            }

            // Add timeline event
            $order->addTimelineEvent(
                'order_created',
                'Smyle-Bestellung erstellt für ' . $order->vehicle_title,
                $buyer->id,
                $buyer->name,
                'buyer'
            );

            // Mark vehicle as no longer available
            $smyleVehicle->update(['is_active' => false]);

            return $order;
        });
    }
}
