<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\ScopedToUserLocation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Place extends Model
{
    use Auditable;
    use ScopedToUserLocation;

    protected $fillable = ['name', 'building_id', 'estimated_guards'];

    // Location comes through the building, which is itself scoped.
    protected static function scopeToLocation(Builder $query, int $locationId): void
    {
        $query->whereHas('building');
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }
}
