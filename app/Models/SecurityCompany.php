<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\ScopedToUserLocation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecurityCompany extends Model
{
    use Auditable;
    use ScopedToUserLocation;

    protected $fillable = ['name', 'location_id', 'contact', 'contract_detail', 'status'];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
