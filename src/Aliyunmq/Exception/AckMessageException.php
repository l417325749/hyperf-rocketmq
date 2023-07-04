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
namespace Hyperf\RocketMQ\Aliyunmq\Exception;

use Hyperf\RocketMQ\Aliyunmq\Constants;
use Hyperf\RocketMQ\Aliyunmq\Model\AckMessageErrorItem;

/**
 * Ack message could fail for some receipt handles,
 *     and AckMessageException will be thrown.
 * All failed receiptHandles are saved in "$ackMessageErrorItems".
 */
class AckMessageException extends MQException
{
    protected $ackMessageErrorItems;

    public function __construct($code, $message, $previousException = null, $requestId = null, $hostId = null)
    {
        parent::__construct($code, $message, $previousException, Constants::ACK_FAIL, $requestId, $hostId);

        $this->ackMessageErrorItems = [];
    }

    public function addAckMessageErrorItem(AckMessageErrorItem $item)
    {
        $this->ackMessageErrorItems[] = $item;
    }

    public function getAckMessageErrorItems()
    {
        return $this->ackMessageErrorItems;
    }
}
