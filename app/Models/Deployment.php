<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deployment extends Model
{
    public const SUPERVISING_OFFICERS = ['Kwasi Ansah', 'Akua Mensah', 'Yaw Boateng'];

    protected $fillable = [
        'building_id',
        'security_company_id',
        'shift_id',
        'supervising_officer',
        'number_of_guards',
        'start_at',
        'end_at',
        'notes',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at'   => 'datetime',
    ];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function securityCompany(): BelongsTo
    {
        return $this->belongsTo(SecurityCompany::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }
}
