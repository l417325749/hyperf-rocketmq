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
namespace Hyperf\RocketMQ\Aliyunmq\Requests;

class ConsumeMessageRequest extends BaseRequest
{
    private $topicName;

    private $consumer;

    private $messageTag;

    private $numOfMessages;

    private $waitSeconds;

    private $trans;

    public function __construct($instanceId, $topicName, $consumer, $numOfMessages, $messageTag = null, $waitSeconds = null)
    {
        parent::__construct($instanceId, 'get', 'topics/' . $topicName . '/messages');

        $this->topicName = $topicName;
        $this->consumer = $consumer;
        $this->messageTag = $messageTag;
        $this->numOfMessages = $numOfMessages;
        $this->waitSeconds = $waitSeconds;
    }

    /**
     * @return mixed
     */
    public function getTopicName()
    {
        return $this->topicName;
    }

    /**
     * @return mixed
     */
    public function getConsumer()
    {
        return $this->consumer;
    }

    public function getMessageTag()
    {
        return $this->messageTag;
    }

    /**
     * @return mixed
     */
    public function getNumOfMessages()
    {
        return $this->numOfMessages;
    }

    public function getWaitSeconds()
    {
        return $this->waitSeconds;
    }

    public function generateBody()
    {
        return null;
    }

    public function setTrans($trans)
    {
        $this->trans = $trans;
    }

    public function generateQueryString()
    {
        $params = ['numOfMessages' => $this->numOfMessages];
        $params['consumer'] = $this->consumer;
        if ($this->instanceId != null && $this->instanceId != '') {
            $params['ns'] = $this->instanceId;
        }
        if ($this->waitSeconds != null) {
            $params['waitseconds'] = $this->waitSeconds;
        }
        if ($this->messageTag != null) {
            $params['tag'] = $this->messageTag;
        }
        if ($this->trans != null) {
            $params['trans'] = $this->trans;
        }
        return http_build_query($params);
    }
}
