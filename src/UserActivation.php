<?php

namespace Rorikurn\Activator;

use Illuminate\Database\Eloquent\Model;

class UserActivation extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'status',
        'expires_at'
    ];

    public function scopeNeedActivation($q, $token)
    {
        return $q->where('token', $token)
            ->where('status', '!=', 'activated');
    }
}
