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
use Hyperf\RocketMQ\Aliyunmq\Exception\MessageNotExistException;
use Hyperf\RocketMQ\Aliyunmq\Exception\MessageResolveException;
use Hyperf\RocketMQ\Aliyunmq\Exception\MQException;
use Hyperf\RocketMQ\Aliyunmq\Exception\TopicNotExistException;
use Hyperf\RocketMQ\Aliyunmq\Model\Message;

class ConsumeMessageResponse extends BaseResponse
{
    protected $messages;

    public function __construct()
    {
        $this->messages = [];
    }

    public function getMessages()
    {
        return $this->messages;
    }

    public function parseResponse($statusCode, $content)
    {
        $this->statusCode = $statusCode;
        if ($statusCode == 200) {
            $this->succeed = true;
        } else {
            $this->parseErrorResponse($statusCode, $content);
        }

        try {
            if ($this->loadAndValidateXmlContent($content, $xmlReader)) {
                while ($xmlReader->read()) {
                    if ($xmlReader->nodeType == \XMLReader::ELEMENT
                        && $xmlReader->name == 'Message') {
                        $this->messages[] = Message::fromXML($xmlReader);
                    }
                }
                return $this->messages;
            }
            throw new MessageResolveException($statusCode, 'Some messages cannot be resolved', MessagePartialResolver::resolve($content));
        } catch (MessageResolveException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new MQException($statusCode, $e->getMessage(), $e);
        } catch (\Throwable $t) {
            throw new MQException($statusCode, $t->getMessage(), $t);
        }
    }

    public function parseErrorResponse($statusCode, $content, MQException $exception = null)
    {
        $this->succeed = false;
        $xmlReader = $this->loadXmlContent($content);

        try {
            $result = XMLParser::parseNormalError($xmlReader);
            if ($result['Code'] == Constants::TOPIC_NOT_EXIST) {
                throw new TopicNotExistException($statusCode, $result['Message'], $exception, $result['Code'], $result['RequestId'], $result['HostId']);
            }
            if ($result['Code'] == Constants::MESSAGE_NOT_EXIST || empty($result['Code'])) {
                throw new MessageNotExistException($statusCode, $result['Message'], $exception, $result['Code'], $result['RequestId'], $result['HostId']);
            }
            throw new MQException($statusCode, $result['Message'], $exception, $result['Code'], $result['RequestId'], $result['HostId']);
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
}
