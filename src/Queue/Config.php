<?php

/*
 * This file is part of the overtrue/weather.
 *
 * (c) jiannei <longjian.huang@foxmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Jiannei\LaravelRabbitMQ\Queue;

use Jiannei\LaravelRabbitMQ\Queue\Jobs\RabbitMQJob;

class Config
{
    protected $queue = 'default';

    protected $dispatchAfterCommit = false;

    protected $abstractJob = RabbitMQJob::class;

    protected $prioritizeDelayed = false;

    protected $queueMaxPriority = 2;

    protected $exchange = '';

    protected $exchangeType = 'direct';

    protected $exchangeRoutingKey = '%s';

    protected $rerouteFailed = false;

    protected $failedExchange = '';

    protected $failedRoutingKey = '%s.failed';

    protected $quorum = false;

    protected $options = [];

    /**
     * Holds the default queue name.
     *
     * When no queue name is provided by laravel queue / workers via the QueueApi method's,
     * this value is used to publish messages.
     */
    public function getQueue(): string
    {
        return $this->queue;
    }

    public function setQueue(string $queue): Config
    {
        $this->queue = $queue;

        return $this;
    }

    /**
     * Returns &true; as indication that jobs should be dispatched after all database transactions
     * have been committed.
     */
    public function isDispatchAfterCommit(): bool
    {
        return $this->dispatchAfterCommit;
    }

    public function setDispatchAfterCommit($dispatchAfterCommit): Config
    {
        $this->dispatchAfterCommit = $this->toBoolean($dispatchAfterCommit);

        return $this;
    }

    /**
     * Get the Job::class to use when processing messages.
     */
    public function getAbstractJob(): string
    {
        return $this->abstractJob;
    }

    public function setAbstractJob(string $abstract): Config
    {
        $this->abstractJob = $abstract;

        return $this;
    }

    /**
     * Returns &true;, if delayed messages should be prioritized.
     *
     * RabbitMQ queues work with the FIFO method. So when there are 10000 messages in the queue and
     * the delayed message is put back to the queue (at the end) for further processing the delayed message won´t
     * process before all 10000 messages are processed. The same is true for requeueing.
     *
     * This may not what you desire.
     * When you want the message to get processed immediately after the delayed time expires or when requeueing, we can
     * use prioritization.
     *
     * @see[https://www.rabbitmq.com/queues.html#basics]
     */
    public function isPrioritizeDelayed(): bool
    {
        return $this->prioritizeDelayed;
    }

    public function setPrioritizeDelayed($prioritizeDelayed): Config
    {
        $this->prioritizeDelayed = $this->toBoolean($prioritizeDelayed);

        return $this;
    }

    /**
     * Returns a integer with a default of '2' for when using prioritization on delayed messages.
     * If priority queues are desired, we recommend using between 1 and 10.
     * Using more priority layers, will consume more CPU resources and would affect runtimes.
     *
     * @see https://www.rabbitmq.com/priority.html
     */
    public function getQueueMaxPriority(): int
    {
        return $this->queueMaxPriority;
    }

    public function setQueueMaxPriority($queueMaxPriority): Config
    {
        if (is_numeric($queueMaxPriority) && intval($queueMaxPriority) > 1) {
            $this->queueMaxPriority = (int) $queueMaxPriority;
        }

        return $this;
    }

    /**
     * Get the exchange name, or empty string; as default value.
     *
     * The default exchange is an unnamed pre-declared direct exchange. Usually, an empty string
     * is frequently used to indicate it. If you choose default exchange, your message will be delivered
     * to a queue with the same name as the routing key.
     * With a routing key that is the same as the queue name, every queue is immediately tied to the default exchange.
     */
    public function getExchange(): string
    {
        return $this->exchange;
    }

    public function setExchange(string $exchange): Config
    {
        $this->exchange = $exchange;

        return $this;
    }

    /**
     * Get the exchange type.
     *
     * There are four basic RabbitMQ exchange types in RabbitMQ, each of which uses different parameters
     * and bindings to route messages in various ways, These are: 'direct', 'topic', 'fanout', 'headers'
     *
     * The default type is set as 'direct'
     */
    public function getExchangeType(): string
    {
        return $this->exchangeType;
    }

    public function setExchangeType(string $exchangeType): Config
    {
        $this->exchangeType = $exchangeType;

        return $this;
    }

    /**
     * Get the routing key when using an exchange other than the direct exchange.
     * The routing key is a message attribute taken into account by the exchange when deciding how to route a message.
     *
     * The default routing-key is the given destination: '%s'.
     */
    public function getExchangeRoutingKey(): string
    {
        return $this->exchangeRoutingKey;
    }

    public function setExchangeRoutingKey(string $exchangeRoutingKey): Config
    {
        $this->exchangeRoutingKey = $exchangeRoutingKey;

        return $this;
    }

    /**
     * Returns &true;, if failed messages should be rerouted.
     */
    public function isRerouteFailed(): bool
    {
        return $this->rerouteFailed;
    }

    public function setRerouteFailed($rerouteFailed): Config
    {
        $this->rerouteFailed = $this->toBoolean($rerouteFailed);

        return $this;
    }

    /**
     * Get the exchange name with messages are published against.
     * The default exchange is empty, so messages will be published directly to a queue.
     */
    public function getFailedExchange(): string
    {
        return $this->failedExchange;
    }

    public function setFailedExchange(string $failedExchange): Config
    {
        $this->failedExchange = $failedExchange;

        return $this;
    }

    /**
     * Get the substitution string for failed messages
     * The default routing-key is the given destination substituted by '%s.failed'.
     */
    public function getFailedRoutingKey(): string
    {
        return $this->failedRoutingKey;
    }

    public function setFailedRoutingKey(string $failedRoutingKey): Config
    {
        $this->failedRoutingKey = $failedRoutingKey;

        return $this;
    }

    /**
     * Returns &true;, if queue is marked or set as quorum queue.
     */
    public function isQuorum(): bool
    {
        return $this->quorum;
    }

    public function setQuorum($quorum): Config
    {
        $this->quorum = $this->toBoolean($quorum);

        return $this;
    }

    /**
     * Holds all unknown queue options provided in the connection config.
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(array $options): Config
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Filters $value to boolean value.
     *
     * Returns: &true;
     * For values: 1, '1', true, 'true', 'yes'
     *
     * Returns: &false;
     * For values: 0, '0', false, 'false', '', null, [] , 'ok', 'no', 'no not a bool', 'yes a bool'
     */
    protected function toBoolean($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
