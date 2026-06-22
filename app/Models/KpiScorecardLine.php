<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class KpiScorecardLine extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'kpi_scorecard_id',
        'kpi_group_id',
        'kpi_sub_item_id',
        'place_id',
        'criteria',
        'target',
        'scored',
        'merit',
    ];

    protected $casts = [
        'merit' => 'decimal:1',
    ];

    public function scorecard(): BelongsTo
    {
        return $this->belongsTo(KpiScorecard::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(KpiGroup::class, 'kpi_group_id');
    }

    public function subItem(): BelongsTo
    {
        return $this->belongsTo(KpiSubItem::class, 'kpi_sub_item_id');
    }

    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class);
    }
}
