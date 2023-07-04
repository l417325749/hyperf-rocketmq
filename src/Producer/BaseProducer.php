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

use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\Logger\LoggerFactory;
use Hyperf\RocketMQ\Annotation\Producer as ProducerAnnotation;
use Hyperf\RocketMQ\ConnectionFactory;
use Hyperf\RocketMQ\Message\ProducerMessageInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

abstract class BaseProducer implements ProducerInterface
{
    protected ?ConnectionFactory $factory = null;

    protected ?LoggerInterface $logger = null;

    public function __construct(
        ContainerInterface $container,
        ConnectionFactory $factory
    ) {
        $this->factory = $factory;
        $this->logger = $container->get(LoggerFactory::class)->get();
    }

    /**
     * 生产消息依赖注入。
     * 通过生产者注解更新消息.
     */
    protected function injectMessageProperty(ProducerMessageInterface $producerMessage): void
    {
        if (class_exists(AnnotationCollector::class)) {
            /** @var ProducerAnnotation $annotation */
            $annotation = AnnotationCollector::getClassAnnotation(get_class($producerMessage), ProducerAnnotation::class);
            if ($annotation) {
                $annotation->topic && $producerMessage->setTopic($annotation->topic);
                $annotation->groupId && $producerMessage->setGroupId($annotation->groupId);
                $annotation->startDeliverTime && $annotation->startDeliverTime > 0 && $producerMessage->setDeliverTime($annotation->startDeliverTime);
                $annotation->messageTag && $producerMessage->setMessageTag($annotation->messageTag);
                if ($annotation->properties) {
                    foreach ($annotation->properties as $property => $value) {
                        $producerMessage->setProperty($property, $value);
                    }
                }
                $messageKey = hash('md5', sprintf('%s:%s:%f', $annotation->topic ?? '', $annotation->groupId ?? '', microtime(true)));
                $producerMessage->setMessageKey($messageKey);
            }
        }
    }
}
