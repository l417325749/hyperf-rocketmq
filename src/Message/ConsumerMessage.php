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
use Hyperf\RocketMQ\Aliyunmq\Model\Message as RocketMQMessage;

abstract class ConsumerMessage extends Message implements ConsumerMessageInterface
{
    /**
     * consume how many messages once, 1~16.
     */
    public int $numOfMessage = 1;

    /**
     * if > 0, means the time(second) the request holden at server if there is no message to consume.
     * If <= 0, means the server will response back if there is no message to consume.
     * It's value should be 1~30.
     */
    public ?int $waitSeconds = 3;

    public int $nums = 1;

    public bool $enable = true;

    public int $maxConsumption = 0;

    public bool $openCoroutine = true;

    protected string $groupId = '';

    /**
     * filter tag for consumer. If not empty, only consume the message which's messageTag is equal to it.
     */
    protected string $messageTag = '';

    abstract public function consumeMessage(RocketMQMessage $message): void;

    public function getGroupId(): string
    {
        return $this->groupId ?? '';
    }

    public function setGroupId(string $groupId): ConsumerMessage
    {
        $this->groupId = $groupId;
        return $this;
    }

    public function getMessageTag(): ?string
    {
        return $this->messageTag;
    }

    public function setMessageTag(string $messageTag): ConsumerMessage
    {
        $this->messageTag = $messageTag;
        return $this;
    }

    public function getNumOfMessage(): int
    {
        return $this->numOfMessage;
    }

    public function setNumOfMessage(int $num): ConsumerMessage
    {
        $this->numOfMessage = $num;
        return $this;
    }

    public function getWaitSeconds(): int
    {
        return $this->waitSeconds;
    }

    public function setWaitSeconds(int $seconds): ConsumerMessage
    {
        $this->waitSeconds = $seconds;
        return $this;
    }

    public function getProcessNums(): int
    {
        return $this->nums;
    }

    public function setProcessNums(int $num): ConsumerMessage
    {
        $this->nums = $num;
        return $this;
    }

    public function isEnable(): bool
    {
        return $this->enable;
    }

    public function setEnable(bool $enable): ConsumerMessage
    {
        $this->enable = $enable;
        return $this;
    }

    public function getMaxConsumption(): int
    {
        return $this->maxConsumption;
    }

    public function setMaxConsumption(int $num): ConsumerMessage
    {
        $this->maxConsumption = $num;
        return $this;
    }

    public function getOpenCoroutine(): bool
    {
        return $this->openCoroutine;
    }

    public function setOpenCoroutine(bool $isOpen): ConsumerMessage
    {
        $this->openCoroutine = $isOpen;
        return $this;
    }

    public function unserialize(string $data)
    {
        return ApplicationContext::getContainer()->get(Packer::class)->unpack($data);
    }

    public function getMqInfo(RocketMQMessage $message): array
    {
        return [
            'topic' => $this->getTopic(),
            'message_tag' => $message->getMessageTag(),
            'message_key' => $message->getMessageKey(),
            'message_id' => $message->getMessageId(),
            'payload' => $message->getMessageBody(),
        ];
    }
}
