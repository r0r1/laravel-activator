<?php

namespace Rorikurn\Activator\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Rorikurn\Activator\UserActivation;
use Illuminate\Contracts\View\Factory as View;

class ActivationController
{
    /**
     * The validation factory implementation.
     *
     * @var ValidationFactory
     */
    protected $validation;

    /**
     * View Instance
     * @var $view
     */
    protected $view;

    /**
     * Create a activation controller instance.
     *
     * @param  ValidationFactory  $validation
     * @return void
     */
    public function __construct(ValidationFactory $validation, View $view)
    {
        $this->validation = $validation;
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
        $this->validation->make($request->all(), [
            'token' => 'required'
        ]);

        if ($this->validation->fails()) {
            return $this->view->make('activator::activation');
        }

        try {
            $userActivated = UserActivation::needActivation($request->get('token'))->first();
        } catch (\Exception $e) {
            throw new \Exception("token not found.", 1);
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