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

class ExchangeDeclareCommand extends Command
{
    protected $signature = 'rabbitmq:exchange-declare
                            {name : The name of the exchange to declare}
                            {connection=rabbitmq : The name of the queue connection to use}
                            {--type=direct}
                            {--durable=1}
                            {--auto-delete=0}';

    protected $description = 'Declare exchange';

    /**
     * @throws Exception
     */
    public function handle(RabbitMQConnector $connector): void
    {
        $config = $this->laravel['config']->get('queue.connections.'.$this->argument('connection'));

        $queue = $connector->connect($config);

        if ($queue->isExchangeExists($this->argument('name'))) {
            $this->warn('Exchange already exists.');

            return;
        }

        $queue->declareExchange(
            $this->argument('name'),
            $this->option('type'),
            (bool) $this->option('durable'),
            (bool) $this->option('auto-delete')
        );

        $this->info('Exchange declared successfully.');
    }
}
