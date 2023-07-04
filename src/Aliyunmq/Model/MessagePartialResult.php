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
namespace Hyperf\RocketMQ\Aliyunmq\Model;

class MessagePartialResult
{
    private $messages;

    private $failResolveMessages;

    public function __construct(array $messages, array $failResolveMessages)
    {
        $this->messages = $messages;
        $this->failResolveMessages = $failResolveMessages;
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @return array
     */
    public function getFailResolveMessages()
    {
        return $this->failResolveMessages;
    }
}
