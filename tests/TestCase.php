<?php

namespace Jiannei\LaravelRabbitMQ\Tests;

use Jiannei\LaravelRabbitMQ\LaravelServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            LaravelServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.debug', true);
        $app['config']->set('logging.default', 'daily');
        $app['config']->set('logging.channels.daily.path', dirname(__DIR__).'/tests/storage/logs/laravel.log');
    }
}
