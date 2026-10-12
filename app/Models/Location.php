<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\ScopedToUserLocation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use Auditable;
    use ScopedToUserLocation;

    protected $fillable = ['name'];

    protected static function scopeToLocation(Builder $query, int $locationId): void
    {
        $query->whereKey($locationId);
    }

    public function occurrences(): HasMany
    {
        return $this->hasMany(IncidentOccurrence::class);
    }

    public function buildings(): HasMany
    {
        return $this->hasMany(Building::class)->orderBy('name');
    }
}
