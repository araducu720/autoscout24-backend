<?php

namespace App\Console\Commands;

use App\Models\PriceAlert;
use App\Notifications\PriceAlertTriggeredNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckPriceAlerts extends Command
{
    protected $signature = 'alerts:check-prices';
    protected $description = 'Check all active price alerts and notify users when conditions are met';

    public function handle(): int
    {
        $alerts = PriceAlert::where('is_active', true)
            ->with(['vehicle', 'user'])
            ->get();

        $triggered = 0;
        $errors = 0;

        foreach ($alerts as $alert) {
            if (!$alert->vehicle || !$alert->user) {
                continue;
            }

            $currentVehiclePrice = (float) $alert->vehicle->price;
            $oldPrice = (float) $alert->current_price;

            // Skip if no price change since last check
            if ($currentVehiclePrice === $oldPrice) {
                continue;
            }

            if (!$alert->shouldTrigger($currentVehiclePrice)) {
                // Update current price even if not triggered
                $alert->update(['current_price' => $currentVehiclePrice]);
                continue;
            }

            try {
                $alert->user->notify(new PriceAlertTriggeredNotification(
                    $alert,
                    $oldPrice,
                    $currentVehiclePrice
                ));

                $alert->trigger($currentVehiclePrice);
                $triggered++;

                $this->info("Alert #{$alert->id} triggered for {$alert->user->email}: {$alert->vehicle->title} ({$oldPrice} → {$currentVehiclePrice})");
            } catch (\Exception $e) {
                $errors++;
                Log::error('Failed to send price alert notification', [
                    'alert_id' => $alert->id,
                    'user_id' => $alert->user_id,
                    'error' => $e->getMessage(),
                ]);
                $this->error("Alert #{$alert->id} failed: {$e->getMessage()}");
            }
        }

        $this->info("Price alert check complete: {$triggered} triggered, {$errors} errors out of {$alerts->count()} active alerts.");

        return self::SUCCESS;
    }
}
