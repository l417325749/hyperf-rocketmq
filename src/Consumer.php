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
namespace Hyperf\RocketMQ;

use Hyperf\Process\ProcessManager;
use Hyperf\RocketMQ\Aliyunmq\Exception\AckMessageException;
use Hyperf\RocketMQ\Aliyunmq\Exception\MessageNotExistException;
use Hyperf\RocketMQ\Aliyunmq\Model\AckMessageErrorItem;
use Hyperf\RocketMQ\Aliyunmq\Model\Message;
use Hyperf\RocketMQ\Message\ConsumerMessageInterface;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Swoole\Coroutine;

class Consumer
{
    use MsgTrait;

    protected ?EventDispatcherInterface $eventDispatcher = null;

    protected ?ConnectionFactory $factory = null;

    protected ?LoggerInterface $logger = null;

    public function __construct(
        ContainerInterface $container,
        ConnectionFactory $factory,
        LoggerInterface $logger
    ) {
        $this->factory = $factory;
        $this->logger = $logger;
        if ($container->has(EventDispatcherInterface::class)) {
            $this->eventDispatcher = $container->get(EventDispatcherInterface::class);
        }
    }

    /**
     * @throws \Throwable
     */
    public function consume(ConsumerMessageInterface $consumerMessage): void
    {
        // 获取消费者
        $config = $this->factory->getConfigs($consumerMessage->getPoolName());
        $connection = $this->factory->getConnection($consumerMessage->getPoolName())->getConnection();
        $consumer = $connection->getConsumer(
            $config->getInstanceId(),
            $consumerMessage->getTopic(),
            $consumerMessage->getGroupId(),
            $consumerMessage->getMessageTag()
        );

        while (ProcessManager::isRunning()) {
            try {
                $messages = $consumer->consumeMessage(
                    $consumerMessage->getNumOfMessage(), // 每次消费消息数量
                    $consumerMessage->getWaitSeconds()   // 等待时间
                );
            } catch (MessageNotExistException $e) {
                continue;
            } catch (\Throwable $exception) {
                $this->logger->error($exception->getMessage());
                Coroutine::sleep(2);
                throw $exception;
            }

            $maxConsumption = $consumerMessage->getMaxConsumption();
            $currentConsumption = 0;
            $messages = $messages ?: [];

            $receiptHandles = [];
            if ($consumerMessage->getOpenCoroutine() && count($messages) > 1) {
                $callback = [];
                foreach ($messages as $key => $message) {
                    $callback[$key] = $this->getCallBack($consumerMessage, $message);
                }
                $receiptHandles[] = parallel($callback);
            } else {
                foreach ($messages as $message) {
                    $receiptHandles[] = call($this->getCallBack($consumerMessage, $message));
                }
            }

            try {
                $receiptHandles = array_filter($receiptHandles);
                $receiptHandles && $consumer->ackMessage($receiptHandles);
                if ($maxConsumption > 0 && ++$currentConsumption >= $maxConsumption) {
                    break;
                }
            } catch (AckMessageException $exception) {
                $this->logger->error('ack_error', ['RequestId' => $exception->getRequestId()]);
                foreach ($exception->getAckMessageErrorItems() as $errorItem) {
                    /* @var AckMessageErrorItem $errorItem */
                    $this->logger->error('ack_error:receipt_handle', [
                        'receiptHandle' => $errorItem->getReceiptHandle(),
                        'errorCode' => $errorItem->getErrorCode(),
                        'errorMessage' => $errorItem->getErrorMessage(),
                    ]);
                }
            } catch (\Throwable $e) {
                $this->logger->error('ConsumerConsumeErr', ['getMessage' => $e->getMessage(), 'getTrace' => $e->getTrace()]);
                break;
            }
        }
    }

    protected function getCallBack(ConsumerMessageInterface $consumerMessage, Message $message): \Closure
    {
        return function () use ($consumerMessage, $message) {
            try {
                $getMessageBody = $this->getMqTraceInfo($message->getMessageBody());
                $this->logger->info('startConsumer', ['getMessageBody' => $getMessageBody ? json_decode($getMessageBody) : '']);
                $message->setMessageBody($getMessageBody);
                $consumerMessage->consumeMessage($message);
                $receiptHandle = $message->getReceiptHandle();
                $this->logger->info('endConsumer', ['receiptHandle' => $receiptHandle]);
                return $receiptHandle;
            } catch (\Throwable $throwable) {
                $this->logger->error('ConsumerGetCallBackErr', ['getMessage' => $throwable->getMessage()]);
                return null;
            }
        };
    }
}
