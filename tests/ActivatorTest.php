<?php

use Rorikurn\Activator\Activator;
use Mockery as m;
use Illuminate\Encryption\Encrypter;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Mail\Mailer as Mail;
use Illuminate\Contracts\View\Factory as View;

class ActivatorTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $encrypter = m::mock(Encrypter::class);
        $encrypter->shouldReceive('encrypt')->once()->andReturn(null);
        $config = m::mock(Config::class);
        $config->shouldReceive('get')->once()->andReturn(null);
        $view = m::mock(View::class);
        $view->shouldReceive('make')->once()->andReturn(null);
        $mail = m::mock(Mail::class);
        $mail->shouldReceive('send')->once()->andReturn(null);
        $this->activator = new Activator($encrypter, $config, $view, $mail);
    }

    public function test_activate()
    {
        $activated = $this->activator->activate(1);
        $this->assertInternalType('boolean', $activated);
    }
}