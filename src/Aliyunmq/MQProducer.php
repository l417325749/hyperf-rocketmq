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

use Hyperf\RocketMQ\Aliyunmq\Exception\InvalidArgumentException;
use Hyperf\RocketMQ\Aliyunmq\Http\HttpClient;
use Hyperf\RocketMQ\Aliyunmq\Model\TopicMessage;
use Hyperf\RocketMQ\Aliyunmq\Requests\PublishMessageRequest;
use Hyperf\RocketMQ\Aliyunmq\Responses\PublishMessageResponse;

class MQProducer
{
    protected $instanceId;

    protected $topicName;

    protected $client;

    public function __construct(HttpClient $client, $instanceId, $topicName)
    {
        if (empty($topicName)) {
            throw new InvalidArgumentException(400, 'TopicName is null');
        }
        $this->instanceId = $instanceId;
        $this->client = $client;
        $this->topicName = $topicName;
    }

    public function getInstanceId()
    {
        return $this->instanceId;
    }

    public function getTopicName()
    {
        return $this->topicName;
    }

    public function publishMessage(TopicMessage $topicMessage)
    {
        $request = new PublishMessageRequest(
            $this->instanceId,
            $this->topicName,
            $topicMessage->getMessageBody(),
            $topicMessage->getProperties(),
            $topicMessage->getMessageTag()
        );
        $response = new PublishMessageResponse();
        return $this->client->sendRequest($request, $response);
    }
}
