<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class IncidentType extends Model
{
    use Auditable;
    use SoftDeletes;

    protected $fillable = ['name'];

    public function occurrences(): HasMany
    {
        return $this->hasMany(IncidentOccurrence::class);
    }
}
