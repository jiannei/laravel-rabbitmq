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

class ExchangeDeleteCommand extends Command
{
    protected $signature = 'rabbitmq:exchange-delete
                            {name : The name of the exchange to delete}
                            {connection=rabbitmq : The name of the queue connection to use}
                            {--unused=0 : Check if exchange is unused}';

    protected $description = 'Delete exchange';

    /**
     * @throws Exception
     */
    public function handle(RabbitMQConnector $connector): void
    {
        $config = $this->laravel['config']->get('queue.connections.'.$this->argument('connection'));

        $queue = $connector->connect($config);

        if (! $queue->isExchangeExists($this->argument('name'))) {
            $this->warn('Exchange does not exist.');

            return;
        }

        $queue->deleteExchange(
            $this->argument('name'),
            (bool) $this->option('unused')
        );

        $this->info('Exchange deleted successfully.');
    }
}
