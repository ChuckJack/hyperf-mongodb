<?php

namespace Timebug\Mongodb\Pool;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\ConnectionInterface;
use Hyperf\Pool\Exception\ConnectionException;
use Hyperf\Pool\Pool;
use Psr\Container\ContainerInterface;
use Timebug\Mongodb\Config\MongoConfig;
use Timebug\Mongodb\Frequency;
use Timebug\Mongodb\MongodbConnection;

class MongodbPool extends Pool
{

    protected string $pool = 'default';

    protected MongoConfig $config;

    public function __construct(ContainerInterface $container, string $pool = 'default')
    {
        $this->pool = $pool;
        $config = $container->get(ConfigInterface::class);
        $key = sprintf('mongodb.%s', $this->pool);
        if (! $config->has($key)) {
            throw new \InvalidArgumentException(sprintf('config[%s] is not exist!', $key));
        }

        $mongoConfig = $config->get($key);
        $this->config = new MongoConfig($mongoConfig);

        $this->frequency = make(Frequency::class);

        parent::__construct($container, $this->config->getPool());
    }

    public function getPool(): string
    {
        return $this->pool;
    }

    /**
     * @throws ConnectionException
     */
    protected function createConnection(): ConnectionInterface
    {
        return new MongodbConnection($this->container, $this, $this->config);
    }
}