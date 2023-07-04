<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Hyperf\RocketMQ;

use Hyperf\Contract\ConnectionInterface;
use Hyperf\Pool\Pool;
use Hyperf\Pool\SimplePool\PoolFactory;
use Hyperf\RocketMQ\Aliyunmq\Config;
use Hyperf\RocketMQ\Aliyunmq\MQClient;
use Psr\Log\LoggerInterface;

class RocketMqConnection implements ConnectionInterface
{
    protected ?MQClient $connection = null;

    protected ?LoggerInterface $logger;

    protected Pool $pool;

    protected float $lastUseTime = 0.0;

    protected string $endpoint = '';

    protected string $accessKey = '';

    protected string $secretKey = '';

    protected string $securityToken = '';

    protected $config;

    public function __construct(
        string $endpoint,
        string $accessKey,
        string $secretKey,
        ?string $securityToken = null,
        ?Config $config = null
    ) {
        $this->endpoint = $endpoint;
        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
        $securityToken && $this->securityToken = $securityToken;
        $config && $this->config = $config;
    }

    public function setPool(PoolFactory $factory, string $poolName = 'default', array $options = []): RocketMqConnection
    {
        $this->pool = $factory->get($poolName, function () {
            return new MQClient(
                $this->endpoint,
                $this->accessKey,
                $this->secretKey,
                $this->securityToken,
                $this->config,
            );
        }, $options);
        return $this;
    }

    public function setLogger(?LoggerInterface $logger): RocketMqConnection
    {
        $this->logger = $logger;
        return $this;
    }

    public function getConnection(): MQClient
    {
        $this->connection = $this->pool->get()->getConnection();
        return $this->connection;
    }

    public function reconnect(): bool
    {
        $this->close();
        $this->getConnection();
        $this->lastUseTime = microtime(true);
        return true;
    }

    public function check(): bool
    {
        $maxIdleTime = $this->pool->getOption()->getMaxIdleTime();
        $now = microtime(true);

        if ($now > $maxIdleTime + $this->lastUseTime) {
            return false;
        }

        $this->lastUseTime = $now;
        return true;
    }

    public function close(): bool
    {
        $this->connection = null;
        return true;
    }

    public function release(): void
    {
        $this->pool->release($this);
    }
}