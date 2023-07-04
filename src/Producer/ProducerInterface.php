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

use Hyperf\RocketMQ\Message\ProducerMessageInterface;

interface ProducerInterface
{
    public function produce(ProducerMessageInterface $producerMessage): bool;
}
