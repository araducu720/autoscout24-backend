<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dispute extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference',
        'transaction_id',
        'opened_by',
        'type',
        'description',
        'status',
        'priority',
        'assigned_to',
        'assigned_at',
        'resolution_notes',
        'resolution_outcome',
        'resolved_by',
        'resolved_at',
        'last_activity_at',
        'proposed_resolution',
        'buyer_accepted_resolution',
        'seller_accepted_resolution',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'resolved_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'buyer_accepted_resolution' => 'boolean',
        'seller_accepted_resolution' => 'boolean',
    ];

    // ──── Type Labels ────
    const TYPE_LABELS = [
        'payment_not_received' => 'Payment Not Received',
        'payment_amount_incorrect' => 'Payment Amount Incorrect',
        'vehicle_not_as_described' => 'Vehicle Not As Described',
        'vehicle_not_delivered' => 'Vehicle Not Delivered',
        'documentation_issues' => 'Documentation Issues',
        'damage_during_handover' => 'Damage During Handover',
        'fraud' => 'Suspected Fraud',
        'other' => 'Other',
    ];

    // ──── Status Labels ────
    const STATUS_LABELS = [
        'open' => 'Open',
        'under_review' => 'Under Review',
        'awaiting_info' => 'Awaiting Info',
        'mediation' => 'Mediation',
        'resolved' => 'Resolved',
        'closed' => 'Closed',
        'escalated' => 'Escalated',
    ];

    // ──── Priority Labels ────
    const PRIORITY_LABELS = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'urgent' => 'Urgent',
    ];

    // ──── Resolution Labels ────
    const RESOLUTION_LABELS = [
        'in_favor_seller' => 'In Favor of Seller',
        'in_favor_dealer' => 'In Favor of Dealer',
        'mutual_agreement' => 'Mutual Agreement',
        'refund_issued' => 'Refund Issued',
        'no_action_required' => 'No Action Required',
        'escalated_to_legal' => 'Escalated to Legal',
    ];

    // ──── Scopes ────
    public function scopeOpen($query)
    {
        return $query->whereNotIn('status', ['resolved', 'closed']);
    }

    // ──── Helpers ────
    public function isOpen(): bool
    {
        return !in_array($this->status, ['resolved', 'closed']);
    }

    public function isResolved(): bool
    {
        return in_array($this->status, ['resolved', 'closed']);
    }

    /**
     * Add a message to this dispute.
     */
    public function addMessage(int $userId, string $message, bool $isSystem = false): DisputeMessage
    {
        $this->update(['last_activity_at' => now()]);

        return $this->messages()->create([
            'user_id'   => $userId,
            'message'   => $message,
            'is_system' => $isSystem,
        ]);
    }

    public function assignTo(int $userId): void
    {
        $this->update([
            'assigned_to' => $userId,
            'assigned_at' => now(),
            'status' => $this->status === 'open' ? 'under_review' : $this->status,
            'last_activity_at' => now(),
        ]);
    }

    public function resolve(string $outcome, string $notes, int $resolvedBy): void
    {
        $this->update([
            'status' => 'resolved',
            'resolution_outcome' => $outcome,
            'resolution_notes' => $notes,
            'resolved_by' => $resolvedBy,
            'resolved_at' => now(),
            'last_activity_at' => now(),
        ]);
    }

    public function escalate(int $escalatedBy, string $reason): void
    {
        $this->update([
            'status' => 'escalated',
            'priority' => 'urgent',
            'last_activity_at' => now(),
        ]);

        $this->messages()->create([
            'user_id' => $escalatedBy,
            'message' => "Dispute escalated: {$reason}",
            'is_system' => true,
        ]);
    }

    // ──── Boot ────
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($dispute) {
            if (empty($dispute->reference)) {
                $dispute->reference = 'DSP-' . strtoupper(uniqid());
            }
            $dispute->last_activity_at = now();
        });
    }

    // ──── Relationships ────
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(SafetradeTransaction::class, 'transaction_id');
    }

    public function openedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(DisputeMessage::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(DisputeAttachment::class);
    }
}
