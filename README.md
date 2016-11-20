# laravel-activator
Laravel activator provides activation &amp; resend activation account.

## Installation
``` bash
composer require rorikurniadi/laravel-activator
```

## Setup

### Register in config/app.php
``` php
    'providers' => [
        #...
        Rorikurn\Activator\ActivatorServiceProvider::class
    ],

    'aliases' => [
        #...
        'Activator' => 'Rorikurn\Activator\Facades\Activator'
    ]
```

### Migration
``` bash
php artisan migrate --path="vendor/rorikurniadi/laravel-activator/database/migrations"
```

### Publish Views & Config File
``` bash
php artisan vendor:publish --force
```

## Usage

Integrate with your Registration Process

``` php
    # app/Http/Controllers/Auth/RegisterController

    protected function create(array $data)
    {
        // Create User
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        return Activator::activate($user);
    }
```

This is beta version.
