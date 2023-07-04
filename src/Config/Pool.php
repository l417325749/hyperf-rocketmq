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

namespace Hyperf\RocketMQ\Config;

class Pool
{
    private int $minConnections = 10;

    private int $maxConnections = 50;

    private int $connectTimeout = 3;

    private int $waitTimeout = 20;

    private int $heartbeat = -1;

    private int $maxIdleTime = 60;

    public function __construct(array $data = [])
    {
        isset($data['min_connections']) && $this->minConnections = $data['min_connections'];
        isset($data['max_connections']) && $this->maxConnections = $data['max_connections'];
        isset($data['connect_timeout']) && $this->connectTimeout = $data['connect_timeout'];
        isset($data['wait_timeout']) && $this->waitTimeout = $data['wait_timeout'];
        isset($data['heartbeat']) && $this->heartbeat = $data['heartbeat'];
        isset($data['max_idle_time']) && $this->maxIdleTime = $data['max_idle_time'];
    }

    public function getMinConnections()
    {
        return $this->minConnections;
    }

    public function getMaxConnections()
    {
        return $this->maxConnections;
    }

    public function getConnectTimeout()
    {
        return $this->connectTimeout;
    }

    public function getWaitTimeout()
    {
        return $this->waitTimeout;
    }

    public function getHeartbeat()
    {
        return $this->heartbeat;
    }

    public function getMaxIdleTime()
    {
        return $this->maxIdleTime;
    }

    public function toArray(): array
    {
        return [
            'min_connections' => $this->minConnections,
            'max_connections' => $this->maxConnections,
            'connect_timeout' => $this->connectTimeout,
            'wait_timeout' => $this->waitTimeout,
            'heartbeat' => $this->heartbeat,
            'max_idle_time' => $this->maxIdleTime,
        ];
    }
}
