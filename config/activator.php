<?php

return [
    // handle laravel notification via mail
    'notification'  => false,

    // User model
    'model'  => App\User::class,

    // redirect url after activation account successful
    'redirect_url' => 'user',

    // expiryTime
    'expiry_time'   => 60
];
