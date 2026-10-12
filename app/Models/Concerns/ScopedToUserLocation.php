<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * Limits queries to the logged-in user's location. Super-Admins, and code running
 * without a logged-in user (console, API token), see every location. A user with
 * no location assigned sees nothing.
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

            if ($user->location_id === null) {
                $query->whereRaw('1 = 0');

                return;
            }

            static::scopeToLocation($query, (int) $user->location_id);
        });
    }

    protected static function scopeToLocation(Builder $query, int $locationId): void
    {
        $query->where($query->qualifyColumn('location_id'), $locationId);
    }
}
