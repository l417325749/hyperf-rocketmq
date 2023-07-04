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
namespace Hyperf\RocketMQ\Message;

use Hyperf\RocketMQ\Packer\Packer;
use Hyperf\Utils\ApplicationContext;

class ProducerMessage extends Message implements ProducerMessageInterface
{
    protected string $groupId = '';

    protected string $messageKey = '';

    protected string $messageTag = '';

    protected $payload = '';

    protected $shardingKey = '';

    protected array $properties = [];

    /**
     * 投递时间(10位时间戳).
     */
    protected ?int $deliverTime = null;

    public function setPayload($payload): ProducerMessage
    {
        $this->payload = $payload;
        return $this;
    }

    public function payload(): string
    {
        return $this->serialize();
    }

    public function getMessageKey(): string
    {
        return $this->messageKey;
    }

    public function setMessageKey(string $messageKey): ProducerMessage
    {
        $this->messageKey = $messageKey;
        return $this;
    }

    public function getMessageTag(): string
    {
        return $this->messageTag;
    }

    public function setMessageTag(string $messageTag): ProducerMessage
    {
        $this->messageTag = $messageTag;
        return $this;
    }

    public function getDeliverTime(): ?int
    {
        return $this->deliverTime ? $this->deliverTime * 1000 : null;
    }

    public function setDeliverTime(int $timestamp): ProducerMessage
    {
        $this->deliverTime = $timestamp;
        return $this;
    }

    public function getGroupId(): string
    {
        return $this->groupId ?? '';
    }

    public function setGroupId(string $groupId): ProducerMessage
    {
        $this->groupId = $groupId;
        return $this;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function setProperties(array $properties = []): ProducerMessage
    {
        $this->properties = $properties;
        return $this;
    }

    public function getProperty(string $key = '')
    {
        return $this->properties[$key] ?? '';
    }

    public function setProperty(string $key, $value): ProducerMessage
    {
        $this->properties[$key] = $value;
        return $this;
    }

    public function getShardingKey()
    {
        return $this->shardingKey ?? '';
    }

    public function setShardingKey($shardingKey): ProducerMessage
    {
        $this->shardingKey = $shardingKey;
        return $this;
    }

    public function serialize(): string
    {
        return ApplicationContext::getContainer()->get(Packer::class)->pack($this->payload);
    }

    public function getProduceInfo(): array
    {
        return [
            'pool' => $this->getPoolName(),
            'topic' => $this->getTopic(),
            'message_key' => $this->getMessageKey(),
            'message_tag' => $this->getMessageTag(),
            'payload' => $this->payload(),
        ];
    }
}
