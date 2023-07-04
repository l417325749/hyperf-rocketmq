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
namespace Hyperf\RocketMQ\Producer;

use Hyperf\RocketMQ\Aliyunmq\Model\TopicMessage;
use Hyperf\RocketMQ\Message\ProducerMessageInterface;
use Hyperf\RocketMQ\MsgTrait;

class Producer extends BaseProducer
{
    use MsgTrait;

    public function produce(ProducerMessageInterface $producerMessage): bool
    {
        return retry(1, function () use ($producerMessage) {
            return $this->produceMessage($producerMessage);
        });
    }

    private function produceMessage(ProducerMessageInterface $producerMessage): bool
    {
        $this->injectMessageProperty($producerMessage);
        // 获取配置
        $config = $this->factory->getConfigs($producerMessage->getPoolName());

        // 新建一条主题消息
        $message = new TopicMessage($producerMessage->payload());
        // 设置自定义属性
        if ($producerMessage->getProperties()) {
            foreach ($producerMessage->getProperties() as $property => $value) {
                $message->putProperty($property, $value);
            }
        }
        // 设置消息分区(顺序消息)
        $producerMessage->getShardingKey() && $message->setShardingKey($producerMessage->getShardingKey());

        // 设置消息Key
        $finalMsgKey = hash('md5', $message->getMessageBodyMD5() . $producerMessage->getMessageKey());
        $producerMessage->getMessageKey() && $message->setMessageKey($producerMessage->getMessageKey());
        // 设置消息Tag
        $producerMessage->getMessageTag() && $message->setMessageTag($finalMsgKey);
        // 设置是否定时发送
        if ($timeInMillis = $producerMessage->getDeliverTime()) {
            $message->setStartDeliverTime($timeInMillis);
        }
        $connection = $this->factory->getConnection($producerMessage->getPoolName())->getConnection();
        $producer = $connection->getProducer($config->getInstanceId(), $producerMessage->getTopic());

        // 发布消息
        $getMessageBody = $message->getMessageBody();
        $message->messageBody = $this->setMqTraceInfo($message->getMessageBody());
        $this->logger->info('startProducer', array_filter([
            'getMessageBody' => $getMessageBody,
            'getMessageBodyMD5' => $message->getMessageBodyMD5(),
            'getMessageId' => $message->getMessageId(),
            'getProperties' => $message->getProperties(),
            'getReceiptHandle' => $message->getReceiptHandle(),
        ]));
        $retMsg = $producer->publishMessage($message);
        $messageId = $retMsg->messageId ?? null;
        $this->logger->info('endProducer', array_filter([
            'messageId' => $messageId,
            'getMessageBody' => $getMessageBody,
            'getMessageBodyMD5' => $message->getMessageBodyMD5(),
            'getMessageId' => $message->getMessageId(),
            'getProperties' => $message->getProperties(),
            'getReceiptHandle' => $message->getReceiptHandle(),
        ]));

        return (bool) $messageId;
    }
}
