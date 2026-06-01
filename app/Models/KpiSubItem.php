<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class KpiSubItem extends Model
{
    use SoftDeletes;

    protected $fillable = ['kpi_group_id', 'criteria', 'target', 'active'];

    protected $casts = [
        'active' => 'boolean',
        'target' => 'integer',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(KpiGroup::class, 'kpi_group_id');
    }
}
