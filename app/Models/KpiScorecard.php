<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class KpiScorecard extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'location_id',
        'building_id',
        'location_name',
        'building_name',
        'date',
        'comments',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(KpiScorecardLine::class);
    }
}
