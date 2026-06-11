<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class IncidentOccurrence extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'incident_type_id',
        'occurred_at',
        'location_id',
        'building_id',
        'place_id',
        'severity_id',
        'incident_status_id',
        'acknowledged_at',
        'user_id',
        'description',
        'attachment_path',
    ];

    protected $casts = [
        'occurred_at'     => 'datetime',
        'acknowledged_at' => 'datetime',
    ];

    public function isAcknowledged(): bool
    {
        return $this->acknowledged_at !== null;
    }

    // Locked = supervisor acknowledged it, OR more than 1 day since it was reported.
    public function isLocked(): bool
    {
        return $this->isAcknowledged() || $this->created_at->lt(now()->subDay());
    }

    public function incidentType(): BelongsTo
    {
        return $this->belongsTo(IncidentType::class)->withTrashed();
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class);
    }

    public function severity(): BelongsTo
    {
        return $this->belongsTo(Severity::class)->withTrashed();
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(IncidentStatus::class, 'incident_status_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
