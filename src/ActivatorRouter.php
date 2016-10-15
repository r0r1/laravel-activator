<?php

namespace Rorikurn\Activator;

use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Routing\Registrar as Router;

class ActivatorRouter
{
    /**
     * Router Instance
     * @var $router
     */
    private $router;

    /**
     * ActivatorRouter Constructor
     * @param Router $router 
     */ 
    public function __construct(Router $router) 
    {
        $this->router = $router;
    }

    /**
     * Get routes
     * @param  array  $options 
     * @return Illuminate\Support\Facades\Route          
     */
    public static function routes(array $options = [])
    {
        $options = array_merge($options, [
            'namespace' => '\Rorikurn\Activator\Http\Controllers',
        ]);

        Route::group($options, function ($router) use ($callback) {
            $this->listRoutes();
        });
    }

    /**
     * List of Routes
     */
    private function listRoutes()
    {
        $this->router->get('/activation', ['uses' => 'ActivationController@index']);
        $this->router->get('/resend-activation', [
            'uses' => 'ResendActivationController@index'
        ]);
        $this->router->post('/resend-activation', [
            'uses' => 'ResendActivationController@store'
        ]);
    }
}
