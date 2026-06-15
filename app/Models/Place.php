<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Place extends Model
{
    protected $fillable = ['name', 'building_id', 'estimated_guards'];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }
}
