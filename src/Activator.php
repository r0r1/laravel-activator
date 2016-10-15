<?php

namespace Rorikurn\Activator;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Contracts\Mail\Mailer as Mail;
use Illuminate\Contracts\View\Factory as View;
use Illuminate\Support\Facades\Route;
use Rorikurn\Activator\UserActivation;
use Carbon\Carbon;

class Activator
{
    /**
     * Encrypter Instance
     * @var $encrypter
     */
    private $encrypter;

    /**
     * Config instance
     * @var $config
     */
    private $config;

    /**
     * View Instance
     * @var $view
     */
    private $view;

    /**
     * Mail Instance
     * @var $mail
     */
    private $mail;

    /**
     * Activator Constructor
     * @param Encrypter $encrypter 
     * @param Config    $config
     * @param View      $view      
     * @param Mail      $mail      
     */ 
    public function __construct(
        Encrypter $encrypter, 
        Config $config,
        View $view,
        Mail $mail
    ) {
        $this->encrypter = $encrypter;
        $this->config = $config;
        $this->view = $view;
        $this->mail = $mail;
    }

    /**
     * Activation Process
     * @param  int $userId
     * @return boolean
     */
    public function activate(int $userId)
    {
        $config = $this->config->get('activator');
        $data = $this->generateData($userId, $config);

        try {
            UserActivation::create($data);
        } catch (\Exception $e) {
            throw new \Exception('Activate account failed.');
        }

        $user = $this->config->get('activator::model');

        return $this->sendMailActivation($user);
    }

    /**
     * Generate Data Activation
     * @param  int $userId 
     * @param  array $config 
     * @return array         
     */
    private function generateData(int $userId, array $config)
    {
        $expiryTime = $config['expiry_time'];
        $data = [
            'user_id'       => $userId,
            'token'         => $this->encrypter->encrypt($userId),
            'expires_at'    => Carbon::now()->addMinutes($expiryTime)
        ];

        return $data;
    }

    /**
     * Send Mail Activation
     * @param  Model $user 
     * @return boolean       
     */
    private function sendMailActivation($user)
    {
        $mailTemplate = $this->view->make('activator::activation');
        return $this->mail->send($mailTemplate, [$user], function ($mail) use ($user) {
            $mail->to($user->email)
                ->subject('Activation Account');
        });
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
