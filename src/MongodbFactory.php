<?php

namespace Timebug\Mongodb;

use Exception;
use Hyperf\Contract\ConfigInterface;

class MongodbFactory
{
    /**
     * @var array|MongodbProxy[]
     */
    protected array $proxies;

    public function __construct(ConfigInterface $config)
    {
        $mongodbConfigs = $config->get('mongodb');
        foreach ($mongodbConfigs as $poolName => $mongodbConfig) {
            $this->proxies[$poolName] = make(MongodbProxy::class, ['pool' => $poolName]);
        }
    }

    /**
     * @param string $poolName
     * @return MongodbProxy
     * @throws Exception
     */
    public function get(string $poolName): MongodbProxy
    {
        $proxy = $this->proxies[$poolName] ?? null;
        if (!$proxy instanceof MongodbProxy) {
            throw new Exception('Invalid mongodb proxy.');
        }
        return $proxy;
    }
}