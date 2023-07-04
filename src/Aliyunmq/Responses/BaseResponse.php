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
namespace Hyperf\RocketMQ\Aliyunmq\Responses;

use Hyperf\RocketMQ\Aliyunmq\Exception\MQException;

abstract class BaseResponse
{
    protected $succeed;

    protected $statusCode;

    // from header
    protected $requestId;

    abstract public function parseResponse($statusCode, $content);

    abstract public function parseErrorResponse($statusCode, $content, MQException $exception = null);

    public function isSucceed()
    {
        return $this->succeed;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function setRequestId($requestId)
    {
        $this->requestId = $requestId;
    }

    public function getRequestId()
    {
        return $this->requestId;
    }

    protected function loadXmlContent($content)
    {
        $xmlReader = new \XMLReader();
        $isXml = $xmlReader->XML($content);
        if ($isXml === false) {
            throw new MQException($this->statusCode, $content);
        }
        try {
            while (@$xmlReader->read());
        } catch (\Exception $e) {
            throw new MQException($this->statusCode, $content);
        }
        $xmlReader->XML($content);
        return $xmlReader;
    }

    protected function loadAndValidateXmlContent($content, &$xmlReader)
    {
        $doc = new \DOMDocument();
        if (! $doc->loadXML($content)) {
            return false;
        }
        $xmlReader = $this->loadXmlContent($content);
        return true;
    }
}
