<?php

namespace Jiannei\LaravelRabbitMQ\Queue\Connectors;

use Exception;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Queue\Connectors\ConnectorInterface;
use Jiannei\LaravelRabbitMQ\Queue\Connection\ConnectionFactory;
use Jiannei\LaravelRabbitMQ\Queue\QueueConfig;
use Jiannei\LaravelRabbitMQ\Queue\RabbitMQQueue;

class RabbitMQConnector implements ConnectorInterface
{
    /**
     * Establish a queue connection.
     *
     * @return RabbitMQQueue
     *
     * @throws Exception
     */
    public function connect(array $config): Queue
    {
        return new RabbitMQQueue(QueueConfig::make($config), ConnectionFactory::make($config));
    }
}
