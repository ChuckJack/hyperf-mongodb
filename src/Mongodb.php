<?php

namespace Timebug\Mongodb;

use Hyperf\Context\Context;
use Timebug\Mongodb\Exception\InvalidMongodbConnectionException;
use Timebug\Mongodb\Pool\PoolFactory;

class Mongodb
{
    /**
     * @var string
     */
    protected string $poolName = 'default';

    /**
     * @param PoolFactory $factory
     */
    public function __construct(protected PoolFactory $factory)
    {
    }

    public function __call($name, $arguments)
    {
        // Get a connection from coroutine context or connection pool.
        $hasContextConnection = Context::has($this->getContextKey());
        $connection = $this->getConnection($hasContextConnection);

        try {
            $connection = $connection->getConnection();
            // Execute the command with the arguments.
            $result = $connection->{$name}(...$arguments);
        } finally {
            // Release connection.
            if (!$hasContextConnection) {
                Context::set($this->getContextKey(), $connection);
                $connection->release();
            }
        }

        return $result;
    }

    /**
     * @param $hasContextConnection
     * @return MongodbConnection
     */
    private function getConnection($hasContextConnection): MongodbConnection
    {
        $connection = null;
        if ($hasContextConnection) {
            $connection = Context::get($this->getContextKey());
        }
        if (!$connection instanceof MongodbConnection) {
            $pool = $this->factory->getPool($this->poolName);
            $connection = $pool->get();
        }
        if (!$connection instanceof MongodbConnection) {
            throw new InvalidMongodbConnectionException('The connection is not a valid MongodbConnection.');
        }
        return $connection;
    }

    /**
     * The key to identify the connection object in coroutine context.
     */
    private function getContextKey(): string
    {
        return sprintf('mongodb.connection.%s', $this->poolName);
    }
}