<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $table = 'staff';

    protected $fillable = [
        'staff_number',
        'full_name',
        'first_name',
        'middle_names',
        'last_name',
        'title',
        'department',
        'section',
        'job_name',
        'position_name',
        'supervisor_number',
        'email',
        'location_name',
    ];
}
