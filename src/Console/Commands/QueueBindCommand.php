<?php

/*
 * This file is part of the jiannei/laravel-rabbitmq.
 *
 * (c) jiannei <longjian.huang@foxmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Jiannei\LaravelRabbitMQ\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Jiannei\LaravelRabbitMQ\Queue\Connectors\RabbitMQConnector;

class QueueBindCommand extends Command
{
    protected $signature = 'rabbitmq:queue-bind                          
                           {queue}
                           {exchange}
                           {connection=rabbitmq : The name of the queue connection to use}
                           {--routing-key= : Bind queue to exchange via routing key}';

    protected $description = 'Bind queue to exchange';

    /**
     * @throws Exception
     */
    public function handle(RabbitMQConnector $connector): void
    {
        $config = $this->laravel['config']->get('queue.connections.'.$this->argument('connection'));

        $queue = $connector->connect($config);

        $queue->bindQueue(
            $this->argument('queue'),
            $this->argument('exchange'),
            (string) $this->option('routing-key')
        );

        $this->info('Queue bound to exchange successfully.');
    }
}
