<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'expiry_date',
        'type',
        'value',
        'usage_limit',
        'description',
    ];


    public function isExpired()
    {
        // 1. If usage limit is 0, it's expired/used up
        if ($this->usage_limit !== null && $this->usage_limit <= 0) {
            return true;
        }

        // 2. If there is no expiration date, it never expires
        if (!$this->expires_at) {
            return false;
        }

        // 3. Check if the date is in the past
        return Carbon::parse($this->expires_at)->isPast();
    }
    
}
