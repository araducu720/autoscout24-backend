<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmyleRegistration extends Model
{
    protected $table = 'smyle_registrations';

    protected $fillable = [
        'smyle_order_id', 'status', 'desired_plate', 'assigned_plate',
        'registration_district', 'registration_number',
        'owner_full_name', 'owner_address', 'owner_date_of_birth', 'owner_id_number',
        'required_documents', 'submitted_documents', 'documents_complete',
        'registration_fee', 'plates_fee', 'notes',
        'submitted_at', 'approved_at', 'completed_at',
    ];

    protected $casts = [
        'required_documents' => 'array',
        'submitted_documents' => 'array',
        'documents_complete' => 'boolean',
        'registration_fee' => 'decimal:2',
        'plates_fee' => 'decimal:2',
        'owner_date_of_birth' => 'date',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function smyleOrder(): BelongsTo
    {
        return $this->belongsTo(SmyleOrder::class);
    }
}
