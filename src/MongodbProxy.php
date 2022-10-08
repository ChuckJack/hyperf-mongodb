<?php

namespace Timebug\Mongodb;


use Timebug\Mongodb\Pool\PoolFactory;

class MongodbProxy extends Mongodb
{
    protected string $poolName;

    public function __construct(PoolFactory $factory, string $pool)
    {
        parent::__construct($factory);

        $this->poolName = $pool;
    }
}