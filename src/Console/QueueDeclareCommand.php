<?php

/*
 * This file is part of the jiannei/laravel-rabbitmq.
 *
 * (c) jiannei <longjian.huang@foxmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Jiannei\LaravelRabbitMQ\Console;

use Exception;
use Illuminate\Console\Command;
use Jiannei\LaravelRabbitMQ\Queue\Connectors\RabbitMQConnector;

class QueueDeclareCommand extends Command
{
    protected $signature = 'rabbitmq:queue-declare
                           {name : The name of the queue to declare}
                           {connection=rabbitmq : The name of the queue connection to use}
                           {--max-priority}
                           {--durable=1}
                           {--auto-delete=0}
                           {--quorum=0}';

    protected $description = 'Declare queue';

    /**
     * @throws Exception
     */
    public function handle(RabbitMQConnector $connector): void
    {
        $config = $this->laravel['config']->get('queue.connections.'.$this->argument('connection'));

        $queue = $connector->connect($config);

        if ($queue->isQueueExists($this->argument('name'))) {
            $this->warn('Queue already exists.');

            return;
        }

        $arguments = [];

        $maxPriority = (int) $this->option('max-priority');
        if ($maxPriority) {
            $arguments['x-max-priority'] = $maxPriority;
        }

        if ($this->option('quorum')) {
            $arguments['x-queue-type'] = 'quorum';
        }

        $queue->declareQueue(
            $this->argument('name'),
            (bool) $this->option('durable'),
            (bool) $this->option('auto-delete'),
            $arguments
        );

        $this->info('Queue declared successfully.');
    }
}
