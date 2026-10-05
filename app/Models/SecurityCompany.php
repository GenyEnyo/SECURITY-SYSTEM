<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;

class SecurityCompany extends Model
{
    use Auditable;

    public const STATUSES = ['active', 'renewing', 'inactive'];

    protected $fillable = ['name', 'contact', 'contract_detail', 'status'];
}
