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

use Hyperf\RocketMQ\Exception\MessageException;

abstract class Message implements MessageInterface
{
    protected string $poolName = 'default';

    protected string $topic = '';

    public function getPoolName(): string
    {
        return $this->poolName;
    }

    public function setPoolName(string $poolName): Message
    {
        $this->poolName = $poolName;
        return $this;
    }

    public function getTopic(): string
    {
        return $this->topic;
    }

    public function setTopic(string $topic): Message
    {
        $this->topic = $topic;
        return $this;
    }

    /**
     * @throws MessageException
     */
    public function serialize(): string
    {
        throw new MessageException('You have to overwrite serialize() method.');
    }

    /**
     * @throws MessageException
     */
    public function unserialize(string $data)
    {
        throw new MessageException('You have to overwrite unserialize() method.');
    }
}
