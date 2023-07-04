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
namespace Hyperf\RocketMQ\Aliyunmq\Signature;

use Hyperf\RocketMQ\Aliyunmq\Constants;
use Hyperf\RocketMQ\Aliyunmq\Requests\BaseRequest;

class Signature
{
    public static function SignRequest($accessKey, BaseRequest &$request)
    {
        $headers = $request->getHeaders();
        $contentMd5 = '';
        if (isset($headers['Content-MD5'])) {
            $contentMd5 = $headers['Content-MD5'];
        }
        $contentType = '';
        if (isset($headers['Content-Type'])) {
            $contentType = $headers['Content-Type'];
        }
        $date = $headers['Date'];
        $queryString = $request->getQueryString();
        $canonicalizedResource = $request->getResourcePath();
        if ($queryString != null) {
            $canonicalizedResource .= '?' . $request->getQueryString();
        }
        if (strpos($canonicalizedResource, '/') !== 0) {
            $canonicalizedResource = '/' . $canonicalizedResource;
        }

        $tmpHeaders = [];
        foreach ($headers as $key => $value) {
            if (strpos($key, Constants::HEADER_PREFIX) === 0) {
                $tmpHeaders[$key] = $value;
            }
        }
        ksort($tmpHeaders);

        $canonicalizedHeaders = implode("\n", array_map(function ($v, $k) { return $k . ':' . $v; }, $tmpHeaders, array_keys($tmpHeaders)));

        $stringToSign = strtoupper($request->getMethod()) . "\n" . $contentMd5 . "\n" . $contentType . "\n" . $date . "\n" . $canonicalizedHeaders . "\n" . $canonicalizedResource;

        return base64_encode(hash_hmac('sha1', $stringToSign, $accessKey, $raw_output = true));
    }
}
