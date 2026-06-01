<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecurityCompany extends Model
{
    public const STATUSES = ['active', 'renewing', 'inactive'];

    protected $fillable = ['name', 'contact', 'contract_detail', 'status'];
}
