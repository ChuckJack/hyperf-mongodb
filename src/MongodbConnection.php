<?php

namespace Timebug\Mongodb;

use Hyperf\Contract\ConnectionInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Pool\Connection as PoolConnection;
use Hyperf\Pool\Exception\ConnectionException;
use Hyperf\Pool\Pool;
use MongoDB\Client;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;
use Timebug\Mongodb\Config\MongoConfig;

class MongodbConnection extends PoolConnection implements ConnectionInterface
{
    /**
     * MongoDB连接
     *
     * @var Client
     */
    protected Client $connection;

    /**
     * Mongo配置
     *
     * @var MongoConfig
     */
    protected MongoConfig $config;

    /**
     * @param ContainerInterface $container
     * @param Pool $pool
     * @param MongoConfig $config
     * @throws ConnectionException
     */
    public function __construct(ContainerInterface $container, Pool $pool, MongoConfig $config)
    {
        parent::__construct($container, $pool);
        $this->config = $config;
        $this->reconnect();
    }

    /**
     * @throws Throwable
     */
    public function __call(string $name, $arguments)
    {
        try {
            $result = $this->connection->{$name}(...$arguments);
        } catch (Throwable $exception) {
            $result = $this->retry($name, $arguments, $exception);
        }
        return $result;
    }

    /**
     * @return $this
     * @throws ConnectionException
     */
    public function getActiveConnection(): static
    {
        if ($this->check()) {
            return $this;
        }

        if (!$this->reconnect()) {
            throw new ConnectionException('Connection reconnect failed.');
        }

        return $this;
    }

    /**
     * @return bool
     * @throws ConnectionException
     */
    public function reconnect(): bool
    {
        $mongodb = new Client($this->config->getDsn(), $this->config->getOptions());
        if (!$mongodb) {
            throw new ConnectionException('Connection reconnect failed.');
        }

        $this->connection = $mongodb;
        $this->lastUseTime = microtime(true);

        return true;
    }

    public function close(): bool
    {
        unset($this->connection);
        return true;
    }

    /**
     * @param $name
     * @param $arguments
     * @param Throwable $exception
     * @return mixed
     * @throws Throwable
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function retry($name, $arguments, Throwable $exception): mixed
    {
        $logger = $this->container->get(StdoutLoggerInterface::class);
        $logger->warning('Mongodb::__call failed, because ' . $exception->getMessage());

        try {
            $this->reconnect();
            $result = $this->connection->{$name}(...$arguments);
        } catch (Throwable $exception) {
            $this->lastUseTime = 0.0;
            throw $exception;
        }

        return $result;
    }
}