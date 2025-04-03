<?php

/*
 * This file is part of the jiannei/laravel-rabbitmq.
 *
 * (c) jiannei <longjian.huang@foxmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Jiannei\LaravelRabbitMQ;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Queue\QueueManager;
use Illuminate\Support\ServiceProvider;
use Jiannei\LaravelRabbitMQ\Console\ConsumeCommand;
use Jiannei\LaravelRabbitMQ\Queue\Connectors\RabbitMQConnector;
use Jiannei\LaravelRabbitMQ\Queue\Consumer;
use Jiannei\LaravelRabbitMQ\Queue\RabbitMQQueue;

class LaravelServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/rabbitmq.php',
            'queue.connections.rabbitmq'
        );

        if ($this->app->runningInConsole()) {
            $this->app->singleton('rabbitmq.consumer', function () {
                return new Consumer(
                    $this->app['queue'],
                    $this->app['events'],
                    $this->app[ExceptionHandler::class],
                    function () {
                        return $this->app->isDownForMaintenance();
                    }
                );
            });

            $this->app->singleton(ConsumeCommand::class, static function ($app) {
                return new ConsumeCommand(
                    $app['rabbitmq.consumer'],
                    $app['cache.store']
                );
            });

            $this->commands([
                Console\ConsumeCommand::class,
                Console\ExchangeDeclareCommand::class,
                Console\ExchangeDeleteCommand::class,
                Console\QueueBindCommand::class,
                Console\QueueDeclareCommand::class,
                Console\QueueDeleteCommand::class,
                Console\QueuePurgeCommand::class,
            ]);
        }
    }

    /**
     * Register the application's event listeners.
     */
    public function boot(): void
    {
        /** @var QueueManager $queue */
        $queue = $this->app['queue'];

        $queue->addConnector('rabbitmq', function () {
            return new RabbitMQConnector();
        });

        /** @var RabbitMQQueue $connection */
        $connection = $queue->connection('rabbitmq');

        $queue->stopping(static function () use ($connection): void {
            $connection->close();
        });
    }
}
