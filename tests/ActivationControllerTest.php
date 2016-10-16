<?php

use Rorikurn\Activator\Activator;
use Mockery as m;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ActivationControllerTest extends TestCase
{
    public function test_should_be_validation_error_token()
    {
        $request = Request::create('/activation', 'GET', []);

        $view = m::mock('Illuminate\Contracts\View\Factory');
        $view->shouldReceive('make')->andReturn('view');
        $validator = m::mock('Illuminate\Contracts\Validation\Factory');
        $validator->shouldReceive('make')->once()
            ->with([], ['token' => 'required'])
            ->andReturn($validator);
        $validator->shouldReceive('fails')->andReturn($validator);

        $controller = new Rorikurn\Activator\Http\Controllers\ActivationController(
            $validator, $view
        );
        $this->assertEquals('view', $controller->index($request));
    }
}