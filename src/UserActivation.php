<?php

namespace Rorikurn\Activator;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class UserActivation extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'status',
        'expires_at'
    ];

    public function user()
    {
        return $this->belongsTo(Config::get('activator::model'));
    }

    public function scopeNeedActivation($q, $token)
    {
        return $q->where('token', $token)
            ->where('status', '!=', 'activated');
    }
}