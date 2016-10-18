<?php

namespace Rorikurn\Activator\Http\Controllers;

use Illuminate\Http\Request;
use Rorikurn\Activator\UserActivation;
use Illuminate\Contracts\View\Factory as View;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Routing\ResponseFactory as Router;
use Carbon\Carbon;

class ActivationController
{
    /**
     * View Instance
     * @var $view
     */
    protected $view;

    /**
     * Auth Instance
     * @var $auth
     */
    protected $auth;

    /**
     * Config Instance
     * @var $config
     */
    protected $config;

    /**
     * Router Instance
     * @var $router
     */
    private $router;

    /**
     * ActivationController Constructor
     * @param View $view 
     * @param Auth $auth 
     * @param Config $config
     * @param Router $router
     */
    public function __construct(View $view, Auth $auth, Config $config, Router $router)
    {
        $this->view = $view;
        $this->auth = $auth;
        $this->config = $config;
        $this->router = $router;
    }

    /**
     * activation token.
     *
     * @param  Request  $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $userActivated = UserActivation::needActivation($request->get('token'))->first();
        if (is_null($userActivated)) {
            return $this->view->make('activator::activation', [
                'message' => 'token not found.'
            ]);
        }

        $user = $this->checkExpiryTime($userActivated);
        if ($user) {
            return $this->view->make('activator::activation', [
                'message' => 'token expired.'
            ]);
        }

        $userActivated->status = 'activated';
        $userActivated->save();

        $this->auth->loginUsingId($userActivated->user);

        return $this->router->redirectTo($this->config->get('redirect_url'));
    }
    
    /**
     * Get Expiration time token of user
     * @param  UserActivation $user
     * @return boolean       
     */
    private function checkExpiryTime($userActivated)
    {
        $expiryTime = Carbon::createFromFormat('Y-m-d H:i:s', $userActivated->expires_at);
        return Carbon::now()->gt($expiryTime);
    }
}