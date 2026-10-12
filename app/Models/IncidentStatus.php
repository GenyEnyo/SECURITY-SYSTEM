<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IncidentStatus extends Model
{
    // Set by the system when an incident is reported / acknowledged, so they stay.
    public const PROTECTED = ['Reported', 'Reviewing'];

    protected $fillable = ['name'];

    public function isProtected(): bool
    {
        return in_array($this->name, self::PROTECTED, true);
    }

    public function occurrences(): HasMany
    {
        return $this->hasMany(IncidentOccurrence::class, 'incident_status_id');
    }
}
