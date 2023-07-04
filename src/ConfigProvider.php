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

use Hyperf\RocketMQ\Listener\BeforeMainServerStartListener;
use Hyperf\RocketMQ\Packer\Packer;
use Hyperf\RocketMQ\Producer\Producer;
use Hyperf\Utils\Packer\JsonPacker;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                Producer::class => Producer::class,
                Packer::class => JsonPacker::class,
                Consumer::class => ConsumerFactory::class,
            ],
            'commands' => [
            ],
            'listeners' => [
                BeforeMainServerStartListener::class => 99,
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'The config for rocketmq.',
                    'source' => __DIR__ . '/../publish/rocketmq.php',
                    'destination' => BASE_PATH . '/config/autoload/rocketmq.php',
                ],
            ],
        ];
    }
}
