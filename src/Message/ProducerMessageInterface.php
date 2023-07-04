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

interface ProducerMessageInterface
{
    public function setPayload($data);

    public function payload(): string;

    public function getProperties(): array;

    public function getMessageKey(): string;

    public function setMessageKey(string $messageKey);

    public function getMessageTag(): string;

    public function setMessageTag(string $messageTag);

    public function getDeliverTime(): ?int;

    public function setDeliverTime(int $timestamp);

    public function getGroupId(): string;

    public function setGroupId(string $groupId);

    public function setProperties(array $properties = []);

    public function getProperty(string $key = '');

    public function setProperty(string $key, $value);

    public function getShardingKey();

    public function setShardingKey($shardingKey);

    public function getProduceInfo(): array;
}
