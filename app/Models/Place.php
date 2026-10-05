<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Place extends Model
{
    use Auditable;

    protected $fillable = ['name', 'building_id', 'estimated_guards'];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }
}
