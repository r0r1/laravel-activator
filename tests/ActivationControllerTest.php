<?php

use Rorikurn\Activator\Activator;
use Mockery as m;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon;

class ActivationControllerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->view = m::mock('Illuminate\Contracts\View\Factory');
        $this->auth = m::mock('Illuminate\Contracts\Auth\Factory');
        $this->config = m::mock('Illuminate\Contracts\Config\Repository');
        $this->router = m::mock('Illuminate\Contracts\Routing\ResponseFactory');
        $this->controller = new Rorikurn\Activator\Http\Controllers\ActivationController(
            $this->view, $this->auth, $this->config, $this->router
        );
    }

    private function createUserActivated($activated, $time)
    {
        if ($activated) {
            $status = 'activated';
        } else {
            $status = 'need_activation';
        }

        $userActivation = new Rorikurn\Activator\UserActivation();
        $userActivation->create([
            'user_id' => 1,
            'token' => 'token123',
            'status' => $status,
            'expires_at' => Carbon::now()->addMinutes($time)
        ]);
        $this->userActivation = $userActivation;
    }

    public function test_token_not_found()
    {
        $this->createUserActivated(true, 60);
        $request = Request::create('/activation', 'GET', ['token' => 'token123']);
        $userActivation = m::mock('Rorikurn\Activator\UserActivation')
            ->shouldReceive('needActivation')
            ->with($request['token'])
            ->andReturn(m::self());

        $this->view->shouldReceive('make')->andReturn('view_token_not_found');
        
        $this->assertEquals('view_token_not_found', $this->controller->index($request));
    }
}