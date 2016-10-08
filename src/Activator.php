<?php

namespace Rorikurn\Activator;

use Illuminate\Database\Eloquent\Model;
use Rorikurn\Activator\UserActivation;
use Illuminate\Support\Facades\Config;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Mail;
use Rorikurn\Activator\Mails\ActivationMail;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;

class Activator
{
    /**
     * User Model
     * 
     * @var $user
     */
    private $user;

    /**
     * Encryptor
     * 
     * @var $crypt
     */
    private $crypt;

    /**
     * Activator Constructor
     * 
     * @param Model $user
     * @param Encrypter $crypt
     */
    public function __construct(Encrypter $crypt)
    {
        $this->user = Config::get('activator::model');
        $this->crypt = $crypt;
    }

    /**
     * Send Email Activation
     * 
     * @return boolean
     */
    public function activate()
    {
        $data = [
            'user_id'       => $this->user->id,
            'token'         => $this->generateToken($this->user->id),
            'expires_at'    => $this->setExpiryTime(Config::get('activator::expiry_time'))
        ];

        try {
            UserActivation::create($data);
        } catch (\Exception $e) {
            throw \Exception('Activate account failed.');
        }

        // send email activation
        if (Config::get('notification')) {
            // laravel notification feature
        }

        return Mail::to($this->user)->send(new ActivationMail);
    }

    /**
     * Generate Token by User Id
     * @param  int $id
     * @return Encrypter
     */
    private function generateToken($id)
    {
        return $this->crypt->encrypt($id);
    }

    /**
     * Set Expiry time of token
     * @param int $value
     */
    private function setExpiryTime($value)
    {
        return Carbon::now() * $value;
    }

    /**
     * Get a Activator route registrar.
     *
     * @param  array  $options
     * @return RouteRegistrar
     */
    public static function routes($callback = null, array $options = [])
    {
        $callback = $callback ?: function ($router) {
            $router->all();
        };
        $options = array_merge($options, [
            'namespace' => '\Rorikurn\Activator\Http\Controllers',
        ]);
        Route::group($options, function ($router) use ($callback) {
            $callback(new RouteRegistrar($router));
        });
    }
}