<?php

use Illuminate\Database\Capsule\Manager as DB;
use Mockery as m;

abstract class TestCase extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->setUpDatabase();
        $this->migrateTables();
    }
    
    protected function setUpDatabase()
    {
        $database = new DB;
        $database->addConnection(['driver' => 'sqlite', 'database' => ':memory:']);
        $database->bootEloquent();
        $database->setAsGlobal();
    }

    protected function migrateTables()
    {
        DB::schema()->create('user_activations', function ($table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('token');
            $table->enum('status', ['need_activation', 'activated', 'resend'])
                ->default('need_activation');
            $table->dateTime('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function tearDown()
    {
        m::close();
    }
}
