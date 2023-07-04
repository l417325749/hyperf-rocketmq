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

use Hyperf\Context\Context;

trait MsgTrait
{
    private function getMqTraceInfo(string $data): string
    {
        $data = $data ? json_decode($data, true) : [];
        if (empty($data)) {
            return '';
        }
        if (! empty($data['mqTraceInfo']['traceId'])) {
            $this->genMqTraceId($data['mqTraceInfo']['traceId']); // 日志放入traceId
        }
        if (! empty($data['mqTraceInfo']['open_tracing_id'])) {
            Context::set('open_tracing_id', $data['mqTraceInfo']['open_tracing_id']); // 日志放入open_tracing_id
        }
        if (! empty($data['mqTraceInfo']['request_id'])) {
            $this->setMqRequestId($data['mqTraceInfo']['request_id']);
        }
        if (! empty($data['mqTraceInfo']['indexStart'])) {
            Context::set('indexStart', $data['mqTraceInfo']['indexStart']); // 日志放入业务开始时间,用于记录耗时
        }
        unset($data['mqTraceInfo']);
        return json_encode($data);
    }

    /**
     * @param array $data
     * @return array
     */
    private function setMqTraceInfo(string $data): string
    {
        $data = $data ? json_decode($data, true) : [];
        if (empty($data)) {
            return '';
        }
        $mqTraceInfo = [];
        $mqTraceInfo['traceId'] = $this->getMqTraceId();
        $mqTraceInfo['request_id'] = $this->getMqRequestId();
        $mqTraceInfo['open_tracing_id'] = Context::get('open_tracing_id', '');
        $mqTraceInfo['indexStart'] = Context::get('indexStart') ?: microtime(true);
        $data['mqTraceInfo'] = $mqTraceInfo;
        return json_encode($data);
    }

    private function setMqRequestId(string $requestId = ''): string
    {
        if ($requestId) {// 请求中携带
            Context::set('requestId', $requestId);
            return $requestId;
        }
        if ($requestId = $this->getMqTraceId()) {// 获取本次请求自己已生成的
            Context::set('requestId', $requestId);
            return $requestId;
        }
        if ($requestId = $this->genMqTraceId()) {// 获取本次请求自己新生成的
            Context::set('requestId', $requestId);
            return $requestId;
        }
        return '';
    }

    private function getMqRequestId(): string
    {
        return Context::get('requestId') ?: $this->setMqRequestId();
    }

    private function getMqTraceId(): string
    {
        return Context::get('traceId') ?: $this->genMqTraceId();
    }

    private function genMqTraceId(string $traceId = ''): string
    {
        if (! $traceId) {
            $traceId = 'PHP_' . env('APP_ENV') . '_' . env('APP_NAME') . '_' . uniqid(gethostname() . '_');
        }
        Context::set('traceId', $traceId);
        return $traceId;
    }
}
