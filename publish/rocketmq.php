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
return [
    'default' => [
        'host' => env('ROCKETMQ_ENDPOINT', ''),
        'access_key' => env('ROCKETMQ_ACCESS_KEY', ''),
        'secret_key' => env('ROCKETMQ_SECRET_KEY', ''),
        'instance_id' => env('ROCKETMQ_INSTANCE_ID', ''),

        'pool' => [
            'min_connections' => 1,
            'max_connections' => 10,
            'connect_timeout' => 10.0,
            'wait_timeout' => 3.0,
            'heartbeat' => -1,
            'max_idle_time' => 60,
        ],
    ],
];
