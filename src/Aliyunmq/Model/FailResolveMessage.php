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

class FailResolveMessage
{
    private $messageId;

    private $receiptHandle;

    private $orgResponseData;

    public function __construct($messageId, $receiptHandle, $orgResponseData)
    {
        $this->messageId = $messageId;
        $this->receiptHandle = $receiptHandle;
        $this->orgResponseData = $orgResponseData;
    }

    /**
     * @return string
     */
    public function getMessageId()
    {
        return $this->messageId;
    }

    /**
     * @return string
     */
    public function getReceiptHandle()
    {
        return $this->receiptHandle;
    }

    /**
     * @return string
     */
    public function getOrgResponseData()
    {
        return $this->orgResponseData;
    }
}
