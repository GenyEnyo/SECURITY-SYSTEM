<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\ScopedToUserLocation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deployment extends Model
{
    use Auditable;
    use ScopedToUserLocation;

    public const FULL_DAY_SHIFT = 'Full Day';

    protected $fillable = [
        'building_id',
        'security_company_id',
        'shift_id',
        'supervisor_id',
        'supervising_officer',
        'number_of_guards',
        'expected_guards',
        'start_at',
        'end_at',
        'notes',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at'   => 'datetime',
    ];

    // Location comes through the building, which is itself scoped.
    protected static function scopeToLocation(Builder $query, int $locationId): void
    {
        $query->whereHas('building');
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function securityCompany(): BelongsTo
    {
        return $this->belongsTo(SecurityCompany::class)->withoutGlobalScope('user_location');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }
}
