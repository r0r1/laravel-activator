<?php

return [
    // handle laravel notification via mail
    'notification'  => true,

    // User model
    'model'  => App\User::class,

    // link activation
    'activation_link' => 'auth/activation',

    // expiryTime
    'expiry_time'   => 60
];
