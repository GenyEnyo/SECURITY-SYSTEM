<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * Limits queries to the logged-in user's location. Super-Admins, and code running
 * without a logged-in user (console, API token), see every location. A user whose
 * staff location doesn't match a location here sees nothing.
 */
trait ScopedToUserLocation
{
    public static function bootScopedToUserLocation(): void
    {
        static::addGlobalScope('user_location', function (Builder $query) {
            $user = auth()->user();

            if (! $user || $user->seesAllLocations()) {
                return;
            }

            // Location comes from the user's staff record (staff.location_name).
            $locationId = $user->locationId();

            if ($locationId === null) {
                $query->whereRaw('1 = 0');

                return;
            }

            static::scopeToLocation($query, $locationId);
        });
    }

    protected static function scopeToLocation(Builder $query, int $locationId): void
    {
        $query->where($query->qualifyColumn('location_id'), $locationId);
    }
}
