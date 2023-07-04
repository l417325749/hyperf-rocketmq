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

use Hyperf\Logger\LoggerFactory;
use Psr\Container\ContainerInterface;

class ConsumerFactory
{
    public function __invoke(ContainerInterface $container): Consumer
    {
        return new Consumer(
            $container,
            $container->get(ConnectionFactory::class),
            $container->get(LoggerFactory::class)->get(),
        );
    }
}
