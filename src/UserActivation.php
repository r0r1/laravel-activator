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
}