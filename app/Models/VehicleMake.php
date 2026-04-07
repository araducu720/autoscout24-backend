<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleMake extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'type',
    ];

    protected $casts = [
        'type' => 'string',
    ];

    public function models(): HasMany
    {
        return $this->hasMany(VehicleModel::class, 'make_id');
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'make_id');
    }
}
