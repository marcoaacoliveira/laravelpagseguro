<?php

namespace Marcoaacoliveira\LaravelPagseguro\Tests;

use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Marcoaacoliveira\LaravelPagseguro\LaravelPagseguroServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected $loadEnvironmentVariables = true;
    public function getPackageProviders($app)
    {
        return [
            LaravelPagseguroServiceProvider::class,
        ];
    }

    protected function setUp(): void
    {
        // make sure, our .env file is loaded
        copy(__DIR__.'/../.env', __DIR__.'/../vendor/orchestra/testbench-core/laravel/.env');
        parent::setUp();
    }

    protected function getEnvironmentSetUp($app)
    {
        $appPath = __DIR__.'/..';
        // make sure, our .env file is loaded
        $app->useEnvironmentPath($appPath);
        $app->bootstrapWith([LoadEnvironmentVariables::class]);
        parent::getEnvironmentSetUp($app);
    }
}
