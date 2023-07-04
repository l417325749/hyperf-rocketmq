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
use Hyperf\RocketMQ\Aliyunmq\Requests\AckMessageRequest;
use Hyperf\RocketMQ\Aliyunmq\Requests\ConsumeMessageRequest;
use Hyperf\RocketMQ\Aliyunmq\Responses\AckMessageResponse;
use Hyperf\RocketMQ\Aliyunmq\Responses\ConsumeMessageResponse;

class MQTransProducer extends MQProducer
{
    private $groupId;

    public function __construct(HttpClient $client, $instanceId, $topicName, $groupId)
    {
        if (empty($groupId)) {
            throw new InvalidArgumentException(400, 'GroupId is null');
        }
        parent::__construct($client, $instanceId, $topicName);
        $this->groupId = $groupId;
    }

    /**
     * consume transaction half message.
     *
     * @return Message
     *
     * @throws TopicNotExistException if queue does not exist
     * @throws MessageNotExistException if no message exists
     * @throws InvalidArgumentException if the argument is invalid
     * @throws MQException if any other exception happends
     */
    public function consumeHalfMessage($numOfMessages, $waitSeconds = -1)
    {
        if ($numOfMessages < 0 || $numOfMessages > 16) {
            throw new InvalidArgumentException(400, 'numOfMessages should be 1~16');
        }
        if ($waitSeconds > 30) {
            throw new InvalidArgumentException(400, 'numOfMessages should less then 30');
        }
        $request = new ConsumeMessageRequest($this->instanceId, $this->topicName, $this->groupId, $numOfMessages, $this->messageTag, $waitSeconds);
        $request->setTrans(Constants::TRANSACTION_POP);
        $response = new ConsumeMessageResponse();
        return $this->client->sendRequest($request, $response);
    }

    /**
     * commit transaction message.
     *
     * @return AckMessageResponse
     *
     * @throws TopicNotExistException if queue does not exist
     * @throws ReceiptHandleErrorException if the receiptHandle is invalid
     * @throws InvalidArgumentException if the argument is invalid
     * @throws AckMessageException if any message not deleted
     * @throws MQException if any other exception happends
     */
    public function commit($receiptHandle)
    {
        $request = new AckMessageRequest($this->instanceId, $this->topicName, $this->groupId, [$receiptHandle]);
        $request->setTrans(Constants::TRANSACTION_COMMIT);
        $response = new AckMessageResponse();
        return $this->client->sendRequest($request, $response);
    }

    /**
     * rollback transaction message.
     *
     * @return AckMessageResponse
     *
     * @throws TopicNotExistException if queue does not exist
     * @throws ReceiptHandleErrorException if the receiptHandle is invalid
     * @throws InvalidArgumentException if the argument is invalid
     * @throws AckMessageException if any message not deleted
     * @throws MQException if any other exception happends
     */
    public function rollback($receiptHandle)
    {
        $request = new AckMessageRequest($this->instanceId, $this->topicName, $this->groupId, [$receiptHandle]);
        $request->setTrans(Constants::TRANSACTION_ROLLBACK);
        $response = new AckMessageResponse();
        return $this->client->sendRequest($request, $response);
    }
}
