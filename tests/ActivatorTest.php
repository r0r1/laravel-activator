<?php

use Rorikurn\Activator\Activator;
use Mockery as m;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Mail\Mailer as Mail;
use Illuminate\Contracts\View\Factory as View;

class ActivatorTest extends TestCase
{
    public function test_activate()
    {
        $encrypter = m::mock('Illuminate\Contracts\Encryption\Encrypter');
        $config = m::mock('Illuminate\Contracts\Config\Repository');
        $view = m::mock('Illuminate\Contracts\View\Factory');
        $mail = m::mock('Illuminate\Contracts\Mail\Mailer');
        $activator = new Activator($encrypter, $config, $view, $mail);

        $config->shouldReceive('get')->andReturn(['expiry_time' => 60]);
        $encrypter->shouldReceive('encrypt')->andReturn('string');
        $view->shouldReceive('make')->andReturn(null);
        $mail->shouldReceive('send')->andReturn(true);
        $activated = $activator->activate(1);
        $this->assertInternalType('boolean', $activated);
    }

    public function tearDown()
    {
        m::close();
    }
}