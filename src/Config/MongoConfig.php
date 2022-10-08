<?php

namespace Timebug\Mongodb\Config;

use Timebug\Mongodb\Exception\InvalidHostException;

class MongoConfig
{
    /**
     * DSN地址
     * @var string
     */
    private string $dsn;

    /**
     * 主机地址
     * @var string[]|array
     */
    private array $host;

    /**
     * 端口号
     *
     * @var int
     */
    private int $port;

    /**
     * 用户名
     *
     * @var string
     */
    private string $username;

    /**
     * 密码
     *
     * @var string
     */
    private string $password;

    /**
     * 数据库
     *
     * @var string
     */
    private string $database;

    /**
     * 认证数据库
     *
     * @var string
     */
    private string $authDatabase;

    /**
     * @var array
     */
    private array $options;

    /**
     * 连接池配置
     *
     * @var array
     */
    private array $pool;

    /**
     * @param array $config MongoDB连接配置
     */
    public function  __construct(protected array $config = [])
    {
        $this->dsn  = $config['dsn'] ?? '';
        $this->host = $config['host'] ?? [];
        $this->port = $config['port'] ?? 27017;
        $this->username = $config['username'] ?? '';
        $this->password = $config['password'] ?? '';
        $this->database = $config['database'] ?? '';
        $this->authDatabase = $config['auth_db'] ?? '';
        $this->setOptions($this->config['options'] ?? []);
        $this->pool = $config['pool'] ?? [];
    }

    /**
     * 获取DSM地址
     *
     * @return string
     * @throws InvalidHostException
     */
    public function getDsn(): string
    {
        if ($this->dsn) {
            return $this->dsn;
        }

        $hosts = [];
        if (!$this->getHost()) {
            throw new InvalidHostException('error mongodb config host');
        }
        foreach ($this->getHost() as $host) {
            $hosts[] = $this->authDatabase == ''
                ? sprintf("%s:%d", $host, $this->getPort())
                : sprintf("%s:%d/%s", $host, $this->getPort(), $this->getAuthDatabase());
        }
        return "mongodb://" . implode(',', $hosts);
    }


    /**
     * @return string
     */
    public function getHost(): mixed
    {
        return $this->host;
    }

    /**
     * @return int
     */
    public function getPort(): mixed
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getUsername(): mixed
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword(): mixed
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getDatabase(): mixed
    {
        return $this->database;
    }

    /**
     * @return string
     */
    public function getAuthDatabase(): mixed
    {
        return $this->authDatabase;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return array
     */
    public function getPool(): mixed
    {
        return $this->pool;
    }

    /**
     * @param array $host
     */
    public function setHost(array $host): void
    {
        $this->host = $host;
    }

    /**
     * @param int $port
     */
    public function setPort(mixed $port): void
    {
        $this->port = $port;
    }

    /**
     * @param string $username
     */
    public function setUsername(mixed $username): void
    {
        $this->username = $username;
    }

    /**
     * @param string $password
     */
    public function setPassword(mixed $password): void
    {
        $this->password = $password;
    }

    /**
     * @param string $database
     */
    public function setDatabase(mixed $database): void
    {
        $this->database = $database;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options): void
    {
        $this->username && $options['username'] = $this->username;
        $this->password && $options['password'] = $this->password;
        $this->options = $options;
    }

    /**
     * @param array $pool
     */
    public function setPool(array $pool): void
    {
        $this->pool = $pool;
    }
}