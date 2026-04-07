<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SmyleOrder extends Model
{
    use SoftDeletes;

    protected $table = 'smyle_orders';

    protected $fillable = [
        'reference', 'buyer_id', 'smyle_vehicle_id', 'vehicle_id',
        'vehicle_title', 'vehicle_price', 'delivery_cost', 'service_fee',
        'registration_fee', 'total_amount', 'deposit_amount', 'remaining_amount',
        'status', 'payment_method', 'payment_status',
        'delivery_postal_code', 'delivery_city', 'delivery_street',
        'delivery_house_number', 'preferred_delivery_date', 'estimated_delivery_date',
        'actual_delivery_date', 'desired_license_plate', 'registration_district',
        'buyer_phone', 'buyer_notes', 'cancellation_reason', 'return_reason',
        'return_deadline', 'deposit_paid_at', 'confirmed_at', 'shipped_at',
        'delivered_at', 'completed_at', 'cancelled_at', 'returned_at',
    ];

    protected $casts = [
        'vehicle_price' => 'decimal:2',
        'delivery_cost' => 'decimal:2',
        'service_fee' => 'decimal:2',
        'registration_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'preferred_delivery_date' => 'date',
        'estimated_delivery_date' => 'date',
        'actual_delivery_date' => 'date',
        'return_deadline' => 'date',
        'deposit_paid_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->reference)) {
                $order->reference = 'AS24-SM-' . strtoupper(base_convert(time(), 10, 36)) . '-' . strtoupper(Str::random(4));
            }

            // Calculate remaining amount
            if (!$order->remaining_amount) {
                $order->remaining_amount = $order->total_amount - $order->deposit_amount;
            }

            // Set return deadline to 14 days after delivery
            if (!$order->return_deadline && $order->estimated_delivery_date) {
                $order->return_deadline = $order->estimated_delivery_date->addDays(14);
            }
        });
    }

    // Relationships
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function smyleVehicle(): BelongsTo
    {
        return $this->belongsTo(SmyleVehicle::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function delivery(): HasOne
    {
        return $this->hasOne(SmyleDelivery::class);
    }

    public function registration(): HasOne
    {
        return $this->hasOne(SmyleRegistration::class);
    }

    public function insurance(): HasOne
    {
        return $this->hasOne(SmyleInsurance::class);
    }

    public function warranty(): HasOne
    {
        return $this->hasOne(SmyleWarranty::class);
    }

    public function financing(): HasOne
    {
        return $this->hasOne(SmyleFinancing::class);
    }

    public function timeline(): HasMany
    {
        return $this->hasMany(SmyleOrderTimeline::class)->orderBy('timestamp', 'desc');
    }

    // Workflow methods
    public function markDepositPaid(string $paymentMethod = 'bank_transfer'): bool
    {
        if ($this->status !== 'pending') return false;

        $this->update([
            'status' => 'deposit_paid',
            'payment_method' => $paymentMethod,
            'payment_status' => 'deposit_paid',
            'deposit_paid_at' => now(),
        ]);

        $this->addTimelineEvent('deposit_paid', 'Anzahlung von €' . $this->deposit_amount . ' erhalten', $this->buyer_id, $this->buyer->name ?? 'Käufer', 'buyer');

        return true;
    }

    public function startQualityCheck(): bool
    {
        if ($this->status !== 'deposit_paid') return false;

        $this->update(['status' => 'quality_check']);
        $this->addTimelineEvent('quality_check_started', 'Qualitätsprüfung gestartet', 0, 'System', 'system');

        return true;
    }

    public function startRegistration(): bool
    {
        if ($this->status !== 'quality_check') return false;

        $this->update(['status' => 'registration']);
        $this->addTimelineEvent('registration_started', 'Fahrzeugzulassung gestartet', 0, 'System', 'system');

        return true;
    }

    public function activateInsurance(): bool
    {
        if (!in_array($this->status, ['registration', 'insurance_active'])) return false;

        $this->update(['status' => 'insurance_active']);
        $this->addTimelineEvent('insurance_activated', 'Versicherung aktiviert', 0, 'System', 'system');

        return true;
    }

    public function markReadyForDelivery(): bool
    {
        if (!in_array($this->status, ['insurance_active', 'registration'])) return false;

        $estimatedDelivery = now()->addWeeks(4);
        $this->update([
            'status' => 'ready_for_delivery',
            'estimated_delivery_date' => $estimatedDelivery,
        ]);
        $this->addTimelineEvent('ready_for_delivery', 'Fahrzeug bereit zur Auslieferung', 0, 'System', 'system');

        return true;
    }

    public function markInTransit(string $trackingNumber = null): bool
    {
        if ($this->status !== 'ready_for_delivery') return false;

        $this->update([
            'status' => 'in_transit',
            'shipped_at' => now(),
        ]);
        $this->addTimelineEvent('in_transit', 'Fahrzeug unterwegs zum Käufer', 0, 'System', 'system');

        return true;
    }

    public function markDelivered(): bool
    {
        if ($this->status !== 'in_transit') return false;

        $returnDeadline = now()->addDays(14);
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
            'actual_delivery_date' => now()->toDateString(),
            'payment_status' => 'fully_paid',
            'return_deadline' => $returnDeadline,
        ]);
        $this->addTimelineEvent('delivered', 'Fahrzeug erfolgreich ausgeliefert', 0, 'System', 'system');

        return true;
    }

    public function complete(): bool
    {
        if ($this->status !== 'delivered') return false;

        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
        $this->addTimelineEvent('completed', 'Bestellung abgeschlossen', 0, 'System', 'system');

        return true;
    }

    public function cancel(string $reason): bool
    {
        if (in_array($this->status, ['completed', 'cancelled', 'returned'])) return false;

        $this->update([
            'status' => 'cancelled',
            'cancellation_reason' => $reason,
            'cancelled_at' => now(),
        ]);
        $this->addTimelineEvent('cancelled', 'Bestellung storniert: ' . $reason, $this->buyer_id, $this->buyer->name ?? 'Käufer', 'buyer');

        return true;
    }

    public function initiateReturn(string $reason): bool
    {
        if ($this->status !== 'delivered') return false;
        if ($this->return_deadline && now()->isAfter($this->return_deadline)) return false;

        $this->update([
            'status' => 'returned',
            'return_reason' => $reason,
            'returned_at' => now(),
            'payment_status' => 'refunded',
        ]);
        $this->addTimelineEvent('returned', '14-Tage Widerrufsrecht ausgeübt: ' . $reason, $this->buyer_id, $this->buyer->name ?? 'Käufer', 'buyer');

        return true;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['completed', 'cancelled', 'returned']);
    }

    public function scopeForBuyer($query, $userId)
    {
        return $query->where('buyer_id', $userId);
    }

    // Accessors
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Warten auf Anzahlung',
            'deposit_paid' => 'Anzahlung erhalten',
            'quality_check' => 'Qualitätsprüfung',
            'registration' => 'Zulassung läuft',
            'insurance_active' => 'Versicherung aktiv',
            'ready_for_delivery' => 'Bereit zur Lieferung',
            'in_transit' => 'Unterwegs',
            'delivered' => 'Ausgeliefert',
            'completed' => 'Abgeschlossen',
            'cancelled' => 'Storniert',
            'returned' => 'Zurückgegeben',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    public function getCanReturnAttribute(): bool
    {
        return $this->status === 'delivered'
            && $this->return_deadline
            && now()->isBefore($this->return_deadline);
    }

    // Timeline helper
    public function addTimelineEvent(string $event, string $description, int $actorId, string $actorName, string $role = 'system', array $metadata = []): SmyleOrderTimeline
    {
        return $this->timeline()->create([
            'event' => $event,
            'description' => $description,
            'actor_id' => $actorId ?: null,
            'actor_name' => $actorName,
            'actor_role' => $role,
            'metadata' => $metadata,
            'timestamp' => now(),
        ]);
    }
}
