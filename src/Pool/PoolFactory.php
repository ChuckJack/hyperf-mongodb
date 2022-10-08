<?php

namespace Timebug\Mongodb\Pool;

use Hyperf\Di\Container;
use Psr\Container\ContainerInterface;

class PoolFactory
{

    /**
     * @var array|MongodbPool[]
     */
    protected array $pools = [];

    public function __construct(protected ContainerInterface $container)
    {

    }

    public function getPool(string $pool = 'default'): MongodbPool
    {
        if (isset($this->pools[$pool])) {
            return $this->pools[$pool];
        }

        if ($this->container instanceof Container) {
            $mongodbPool = $this->container->make(MongodbPool::class, ['name' => $pool]);
        } else {
            $mongodbPool = new MongodbPool($this->container, $pool);
        }
        $this->pools[$pool] = $mongodbPool;
        return $mongodbPool;
    }
}