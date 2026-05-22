<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Severity extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'color'];

    public function occurrences(): HasMany
    {
        return $this->hasMany(IncidentOccurrence::class);
    }
}
