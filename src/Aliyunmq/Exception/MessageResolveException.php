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

use Hyperf\RocketMQ\Aliyunmq\Model\MessagePartialResult;

class MessageResolveException extends MQException
{
    private $partialResult;

    public function __construct(
        $code,
        $message,
        MessagePartialResult $result,
        $previousException = null,
        $onsErrorCode = null,
        $requestId = null,
        $hostId = null
    ) {
        parent::__construct($code, $message, $previousException, $onsErrorCode, $requestId, $hostId);
        $this->partialResult = $result;
    }

    /**
     * @return MessagePartialResult
     */
    public function getPartialResult()
    {
        return $this->partialResult;
    }
}
