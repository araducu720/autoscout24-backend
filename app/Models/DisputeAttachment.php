<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisputeAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'dispute_id',
        'uploaded_by',
        'title',
        'description',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function dispute(): BelongsTo
    {
        return $this->belongsTo(Dispute::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
