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
namespace Hyperf\RocketMQ\Aliyunmq;

use Hyperf\RocketMQ\Aliyunmq\Exception\MQException;

class AsyncCallback
{
    protected $succeedCallback;

    protected $failedCallback;

    public function __construct(callable $succeedCallback, callable $failedCallback)
    {
        $this->succeedCallback = $succeedCallback;
        $this->failedCallback = $failedCallback;
    }

    public function onSucceed($result)
    {
        return call_user_func($this->succeedCallback, $result);
    }

    public function onFailed(MQException $e)
    {
        return call_user_func($this->failedCallback, $e);
    }
}
