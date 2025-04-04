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
use Illuminate\Console\ConfirmableTrait;
use Jiannei\LaravelRabbitMQ\Queue\Connectors\RabbitMQConnector;

class QueuePurgeCommand extends Command
{
    use ConfirmableTrait;

    protected $signature = 'rabbitmq:queue-purge
                           {queue}
                           {connection=rabbitmq : The name of the queue connection to use}
                           {--force : Force the operation to run when in production}';

    protected $description = 'Purge all messages in queue';

    /**
     * @throws Exception
     */
    public function handle(RabbitMQConnector $connector): void
    {
        if (! $this->confirmToProceed()) {
            return;
        }

        $config = $this->laravel['config']->get('queue.connections.'.$this->argument('connection'));

        $queue = $connector->connect($config);

        $queue->purge($this->argument('queue'));

        $this->info('Queue purged successfully.');
    }
}
