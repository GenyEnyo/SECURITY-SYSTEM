<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecurityCompanyStatus extends Model
{
    // Used by the system (new companies start active, analytics flags renewing), so they stay.
    public const PROTECTED = ['active', 'renewing', 'inactive'];

    protected $fillable = ['name'];

    public function isProtected(): bool
    {
        return in_array($this->name, self::PROTECTED, true);
    }
}
