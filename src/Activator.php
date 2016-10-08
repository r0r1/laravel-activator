<?php

namespace Rorikurn\Activator;

use Illuminate\Database\Eloquent\Model;
use Rorikurn\Activator\UserActivation;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use Illuminate\Encryption\Encrypter;

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
    public function __construct(Model $user, Encrypter $crypt)
    {
        $this->user = $user;
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
            
        } else {

        }
    }

    public function activation()
    {

    }

    public function resendActivation()
    {

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
}