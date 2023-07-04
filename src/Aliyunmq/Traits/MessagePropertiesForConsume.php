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
namespace Hyperf\RocketMQ\Aliyunmq\Traits;

use Hyperf\RocketMQ\Aliyunmq\Constants;

trait MessagePropertiesForConsume
{
    use MessagePropertiesForPublish;

    protected $publishTime;

    protected $nextConsumeTime;

    protected $firstConsumeTime;

    protected $consumedTimes;

    /**
     * @return mixed
     */
    public function getMessageBody()
    {
        return $this->messageBody;
    }

    /**
     * @return mixed
     */
    public function getPublishTime()
    {
        return $this->publishTime;
    }

    /**
     * @return mixed
     */
    public function getNextConsumeTime()
    {
        return $this->nextConsumeTime;
    }

    /**
     * 对于顺序消费没有意义.
     *
     * @return mixed
     */
    public function getFirstConsumeTime()
    {
        return $this->firstConsumeTime;
    }

    /**
     * @return mixed
     */
    public function getConsumedTimes()
    {
        return $this->consumedTimes;
    }

    public function getProperty($key)
    {
        if ($this->properties == null) {
            return null;
        }
        return $this->properties[$key];
    }

    /**
     * 消息KEY.
     */
    public function getMessageKey()
    {
        return $this->getProperty(Constants::MESSAGE_PROPERTIES_MSG_KEY);
    }

    /**
     * 定时消息时间戳，单位毫秒（ms.
     */
    public function getStartDeliverTime()
    {
        $temp = $this->getProperty(Constants::MESSAGE_PROPERTIES_TIMER_KEY);
        if ($temp === null) {
            return 0;
        }
        return (int) $temp;
    }

    /**
     * 事务消息第一次消息回查的最快时间，单位秒.
     */
    public function getTransCheckImmunityTime()
    {
        $temp = $this->getProperty(Constants::MESSAGE_PROPERTIES_TRANS_CHECK_KEY);
        if ($temp === null) {
            return 0;
        }
        return (int) $temp;
    }

    /**
     * 顺序消息分区KEY.
     */
    public function getShardingKey()
    {
        return $this->getProperty(Constants::MESSAGE_PROPERTIES_SHARDING);
    }
}
