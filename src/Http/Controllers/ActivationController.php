<?php

namespace Rorikurn\Activator\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Rorikurn\Activator\UserActivation;
use Illuminate\Contracts\View\Factory as View;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Contracts\Config\Repository as Config;

class ActivationController
{
    /**
     * View Instance
     * @var $view
     */
    protected $view;

    /**
     * Create a activation controller instance.
     *
     * @param  View  $view
     * @return void
     */
    public function __construct(View $view)
    {
        $this->view = $view;
    }

    /**
     * activation token.
     *
     * @param  Request  $request
     * @return mixed
     */
    public function index(Request $request)
    {
        try {
            $userActivated = UserActivation::needActivation($request->get('token'))->first();
        } catch (\Exception $e) {
            return $this->view->make('activator::activation', [
                'message' => 'token not found.'
            ]);
        }

        $expiryTime = $this->getExpiryTime($userActivated);
        if ($expiryTime) {
            return View::make('activator::activation_expired');
        }

        $userActivated->status = 'activated';
        $userActivated->save();
        Auth::loginUsingId($userActivated->user->id);

        return Redirect::to(Config::get('redirect_url'));
    }
    
    /**
     * Get Expiration time token of user
     * @param  UserActivation $user
     * @return bool       
     */
    private function getExpiryTime($user)
    {
        if ($user->expires_at > Carbon::now()) {
            return false;
        }
        return true;
    }
}