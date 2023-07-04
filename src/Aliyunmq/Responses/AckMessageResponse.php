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

use Hyperf\RocketMQ\Aliyunmq\Common\XMLParser;
use Hyperf\RocketMQ\Aliyunmq\Constants;
use Hyperf\RocketMQ\Aliyunmq\Exception\AckMessageException;
use Hyperf\RocketMQ\Aliyunmq\Exception\InvalidArgumentException;
use Hyperf\RocketMQ\Aliyunmq\Exception\MQException;
use Hyperf\RocketMQ\Aliyunmq\Exception\ReceiptHandleErrorException;
use Hyperf\RocketMQ\Aliyunmq\Exception\TopicNotExistException;
use Hyperf\RocketMQ\Aliyunmq\Model\AckMessageErrorItem;

class AckMessageResponse extends BaseResponse
{
    public function __construct()
    {
    }

    public function parseResponse($statusCode, $content)
    {
        $this->statusCode = $statusCode;
        if ($statusCode == 204) {
            $this->succeed = true;
        } else {
            $this->parseErrorResponse($statusCode, $content);
        }
    }

    public function parseErrorResponse($statusCode, $content, MQException $exception = null)
    {
        $this->succeed = false;
        $xmlReader = $this->loadXmlContent($content);

        try {
            while (@$xmlReader->read()) {
                if ($xmlReader->nodeType == \XMLReader::ELEMENT) {
                    switch ($xmlReader->name) {
                        case Constants::ERROR:
                            $this->parseNormalErrorResponse($xmlReader);
                            break;
                        default: // case Constants::Messages
                            $this->parseAckMessageErrorResponse($xmlReader);
                            break;
                    }
                }
            }
        } catch (\Exception $e) {
            if ($exception != null) {
                throw $exception;
            }
            if ($e instanceof MQException) {
                throw $e;
            }
            throw new MQException($statusCode, $e->getMessage());
        } catch (\Throwable $t) {
            throw new MQException($statusCode, $t->getMessage());
        }
    }

    private function parseAckMessageErrorResponse($xmlReader)
    {
        $ex = new AckMessageException($this->statusCode, 'AckMessage Failed For Some ReceiptHandles');
        $ex->setRequestId($this->getRequestId());
        while ($xmlReader->read()) {
            if ($xmlReader->nodeType == \XMLReader::ELEMENT && $xmlReader->name == Constants::ERROR) {
                $ex->addAckMessageErrorItem(AckMessageErrorItem::fromXML($xmlReader));
            }
        }
        throw $ex;
    }

    private function parseNormalErrorResponse($xmlReader)
    {
        $result = XMLParser::parseNormalError($xmlReader);

        if ($result['Code'] == Constants::INVALID_ARGUMENT) {
            throw new InvalidArgumentException($this->getStatusCode(), $result['Message'], null, $result['Code'], $result['RequestId'], $result['HostId']);
        }
        if ($result['Code'] == Constants::TOPIC_NOT_EXIST) {
            throw new TopicNotExistException($this->getStatusCode(), $result['Message'], null, $result['Code'], $result['RequestId'], $result['HostId']);
        }
        if ($result['Code'] == Constants::RECEIPT_HANDLE_ERROR) {
            throw new ReceiptHandleErrorException($this->getStatusCode(), $result['Message'], null, $result['Code'], $result['RequestId'], $result['HostId']);
        }

        throw new MQException($this->getStatusCode(), $result['Message'], null, $result['Code'], $result['RequestId'], $result['HostId']);
    }
}
