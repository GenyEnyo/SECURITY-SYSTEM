<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Staff extends Model
{
    use Auditable;

    protected $table = 'staff';

    protected $fillable = [
        'staff_number',
        'full_name',
        'first_name',
        'middle_names',
        'last_name',
        'title',
        'department',
        'section',
        'job_name',
        'position_name',
        'supervisor_number',
        'email',
        'location_name',
        // Admin correction (not from HR): used instead of location_name
        // until HR changes this person's location.
        'location_id',
        'location_hr_name_at_override',
    ];

    /** The admin's corrected location, if any. */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class)->withoutGlobalScope('user_location');
    }

    public function hasLocationOverride(): bool
    {
        return $this->location_id !== null;
    }

    /** The app location this staff member belongs to: the admin's correction, else HR's location by name. */
    public function effectiveLocation(): ?Location
    {
        if ($this->location_id !== null) {
            return $this->location;
        }

        if (blank($this->location_name)) {
            return null;
        }

        return Location::withoutGlobalScope('user_location')
            ->whereRaw('LOWER(name) = ?', [strtolower(trim($this->location_name))])
            ->first();
    }
}
