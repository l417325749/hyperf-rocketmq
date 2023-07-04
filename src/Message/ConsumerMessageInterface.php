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

use Hyperf\RocketMQ\Aliyunmq\Model\Message as RocketMQMessage;

interface ConsumerMessageInterface extends MessageInterface
{
    public function consumeMessage(RocketMQMessage $message);

    public function getGroupId(): string;

    public function setGroupId(string $groupId);

    public function getMessageTag(): ?string;

    public function setMessageTag(string $messageTag);

    public function getNumOfMessage(): int;

    public function setNumOfMessage(int $num);

    public function getWaitSeconds(): int;

    public function setWaitSeconds(int $seconds);

    public function getProcessNums(): int;

    public function setProcessNums(int $num);

    public function isEnable(): bool;

    public function setEnable(bool $enable);

    public function getMaxConsumption(): int;

    public function setMaxConsumption(int $num);

    public function getOpenCoroutine(): bool;

    public function setOpenCoroutine(bool $isOpen);

    public function getMqInfo(RocketMQMessage $message): array;
}
