<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class IncidentType extends Model
{
    use SoftDeletes;

    protected $fillable = ['name'];

    public function occurrences(): HasMany
    {
        return $this->hasMany(IncidentOccurrence::class);
    }
}
