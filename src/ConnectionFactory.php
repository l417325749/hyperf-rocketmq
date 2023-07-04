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

use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Pool\SimplePool\PoolFactory;
use Hyperf\RocketMQ\Aliyunmq\Config as MqConfig;
use Hyperf\RocketMQ\Config\Config;
use Psr\Container\ContainerInterface;

class ConnectionFactory
{
    protected ContainerInterface $container;

    protected ConfigInterface $config;

    protected string $poolName = 'default';

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->config = $this->container->get(ConfigInterface::class);
    }

    public function getConnection(string $pool = 'default'): RocketMqConnection
    {
        $config = $this->getConfigs($pool);
        return $this->make($config);
    }

    public function make(Config $config): RocketMqConnection
    {
        $endpoint = $config->getEndpoint() ?? '';
        $accessKey = $config->getAccessKey() ?? '';
        $secretKey = $config->getSecretKey() ?? '';

        $connection = new RocketMqConnection(
            $endpoint,
            $accessKey,
            $secretKey,
            null,
            $this->getMQConfig($config),
        );

        $connection->setPool(
            $this->container->get(PoolFactory::class),
            $this->poolName,
            $config->getPool()->toArray(),
        )->setLogger(
            $this->container->get(StdoutLoggerInterface::class)
        );
        return $connection;
    }

    public function setPoolName(string $poolName = 'default'): ConnectionFactory
    {
        $this->poolName = $poolName;
        return $this;
    }

    public function getConfig(string $poolName = 'default'): array
    {
        $poolName = $poolName == '' ? $this->poolName : $poolName;
        $key = sprintf('rocketmq.%s', $poolName);
        if (! $this->config->has($key)) {
            throw new \InvalidArgumentException(sprintf('config[%s] is not exist!', $key));
        }

        return $this->config->get($key);
    }

    public function getConfigs(string $poolName = 'default'): Config
    {
        $poolName = $poolName == '' ? $this->poolName : $poolName;
        $key = sprintf('rocketmq.%s', $poolName);
        if (! $this->config->has($key)) {
            throw new \InvalidArgumentException(sprintf('config[%s] is not exist!', $key));
        }

        return new Config($this->config->get($key, []));
    }

    protected function getMQConfig(Config $config): MqConfig
    {
        $pool = $config->getPool();

        $conf = new MqConfig();
        $conf->setConnectTimeout($pool->getConnectTimeout() ?? 3);
        $conf->setRequestTimeout($pool->getWaitTimeout() ?? 60);
        return $conf;
    }
}
