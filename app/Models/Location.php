<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use Auditable;

    protected $fillable = ['name'];

    public function occurrences(): HasMany
    {
        return $this->hasMany(IncidentOccurrence::class);
    }

    public function buildings(): HasMany
    {
        return $this->hasMany(Building::class)->orderBy('name');
    }
}
