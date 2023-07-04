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

class Config
{
    private string $endpoint = '';

    private string $accessKey = '';

    private string $secretKey = '';

    private string $instanceId = '';

    private Pool $pool;

    public function __construct(array $data = [])
    {
        isset($data['host']) && $this->endpoint = $data['host'];
        isset($data['access_key']) && $this->accessKey = $data['access_key'];
        isset($data['secret_key']) && $this->secretKey = $data['secret_key'];
        isset($data['instance_id']) && $this->instanceId = $data['instance_id'];
        isset($data['pool']) && $this->pool = new Pool($data['pool'] ?? []);
    }

    public function getEndpoint()
    {
        return $this->endpoint;
    }

    public function getAccessKey()
    {
        return $this->accessKey;
    }

    public function getSecretKey()
    {
        return $this->secretKey;
    }

    public function getInstanceId()
    {
        return $this->instanceId ?? '';
    }

    public function getPool(): Pool
    {
        return $this->pool;
    }

    public function toArray(): array
    {
        return [
            'endpoint' => $this->endpoint,
            'access_key' => $this->getAccessKey(),
            'secret_key' => $this->getSecretKey(),
            'instance_id' => $this->getInstanceId(),
            'pool' => $this->pool->toArray(),
        ];
    }
}
