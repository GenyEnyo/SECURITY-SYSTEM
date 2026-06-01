<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class KpiGroup extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'weight', 'active', 'criteria_label', 'target_label'];

    protected $casts = [
        'active' => 'boolean',
        'weight' => 'integer',
    ];

    public function subItems(): HasMany
    {
        return $this->hasMany(KpiSubItem::class)->orderBy('id');
    }
}
