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

use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\Process\AbstractProcess;
use Hyperf\Process\ProcessManager;
use Hyperf\RocketMQ\Annotation\Consumer as ConsumerAnnotation;
use Hyperf\RocketMQ\Message\ConsumerMessageInterface;
use Psr\Container\ContainerInterface;

class ConsumerManager
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function run(): void
    {
        $classes = AnnotationCollector::getClassesByAnnotation(ConsumerAnnotation::class);
        /**
         * @var string $class
         * @var ConsumerAnnotation $annotation
         */
        foreach ($classes as $class => $annotation) {
            $instance = make($class);
            if (! $instance instanceof ConsumerMessageInterface) {
                continue;
            }

            $annotation->poolName && $instance->setPoolName($annotation->poolName);
            $annotation->topic && $instance->setTopic($annotation->topic);
            $annotation->groupId && $instance->setGroupId($annotation->groupId);
            $annotation->messageTag && $instance->setMessageTag($annotation->messageTag);
            $annotation->numOfMessage && $instance->setNumOfMessage($annotation->numOfMessage);
            $annotation->waitSeconds && $instance->setWaitSeconds($annotation->waitSeconds);
            $annotation->enable && $instance->setEnable($instance->isEnable());
            $annotation->maxConsumption && $instance->setMaxConsumption($annotation->maxConsumption);
            $annotation->openCoroutine && $instance->setOpenCoroutine($annotation->openCoroutine);
            property_exists($instance, 'container') && $instance->container = $this->container;

            $process = $this->createProcess($instance);
            $process->nums = (int) $annotation->nums;
            if ($instance->getMessageTag()) {
                $process->name = $annotation->name . '-' . $instance->getTopic() . '-' . $instance->getMessageTag();
            } else {
                $process->name = $annotation->name . '-' . $instance->getTopic();
            }
            ProcessManager::register($process);
        }
    }

    private function createProcess(ConsumerMessageInterface $consumerMessage): AbstractProcess
    {
        return new class($this->container, $consumerMessage) extends AbstractProcess {
            private Consumer $consumer;

            private ConsumerMessageInterface $consumerMessage;

            public function __construct(ContainerInterface $container, ConsumerMessageInterface $consumerMessage)
            {
                parent::__construct($container);
                $this->consumer = $container->get(Consumer::class);
                $this->consumerMessage = $consumerMessage;
            }

            public function handle(): void
            {
                $this->consumer->consume($this->consumerMessage);
            }

            public function getConsumerMessage(): ConsumerMessageInterface
            {
                return $this->consumerMessage;
            }

            public function isEnable($server): bool
            {
                return $this->consumerMessage->isEnable();
            }
        };
    }
}
