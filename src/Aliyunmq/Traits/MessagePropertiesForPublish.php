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
namespace Hyperf\RocketMQ\Aliyunmq\Traits;

use Hyperf\RocketMQ\Aliyunmq\Constants;
use Hyperf\RocketMQ\Aliyunmq\Exception\MQException;

trait MessagePropertiesForPublish
{
    public $messageId;

    public $messageBodyMD5;

    public $messageBody;

    public $messageTag;

    // only transaction msg have.
    protected $receiptHandle;

    protected $properties;

    public function getMessageBody()
    {
        return $this->messageBody;
    }

    public function setMessageBody($messageBody)
    {
        $this->messageBody = $messageBody;
    }

    public function getMessageTag()
    {
        return $this->messageTag;
    }

    public function setMessageTag($messageTag)
    {
        $this->messageTag = $messageTag;
    }

    public function getMessageId()
    {
        return $this->messageId;
    }

    public function setMessageId($messageId)
    {
        $this->messageId = $messageId;
    }

    public function getMessageBodyMD5()
    {
        return $this->messageBodyMD5;
    }

    public function setMessageBodyMD5($messageBodyMD5)
    {
        $this->messageBodyMD5 = $messageBodyMD5;
    }

    public function getReceiptHandle()
    {
        return $this->receiptHandle;
    }

    public function setReceiptHandle($receiptHandle)
    {
        return $this->receiptHandle = $receiptHandle;
    }

    public function getProperties()
    {
        return $this->properties;
    }

    public function writeMessagePropertiesForPublishXML(\XMLWriter $xmlWriter)
    {
        if ($this->messageBody != null) {
            $xmlWriter->writeElement(Constants::MESSAGE_BODY, $this->messageBody);
        }
        if ($this->messageTag !== null) {
            $xmlWriter->writeElement(Constants::MESSAGE_TAG, $this->messageTag);
        }
        if ($this->properties !== null && sizeof($this->properties) > 0) {
            $this->checkPropValid();
            $xmlWriter->writeElement(
                Constants::MESSAGE_PROPERTIES,
                implode('|', array_map(function ($v, $k) { return $k . ':' . $v; }, $this->properties, array_keys($this->properties)))
            );
        }
    }

    private function checkPropValid()
    {
        foreach ($this->properties as $key => $value) {
            if ($key === null || $key == '' || $value === null || $value == '') {
                throw new MQException(400, 'Message Properties is null or empty');
            }

            if ($this->isContainSpecialChar($key) || $this->isContainSpecialChar($value)) {
                throw new MQException(400, "Message's property can't contains: & \" ' < > : |");
            }
        }
    }

    private function isContainSpecialChar($str)
    {
        return strpos($str, '&') !== false
            || strpos($str, '"') !== false || strpos($str, "'") !== false
            || strpos($str, '<') !== false || strpos($str, '>') !== false
            || strpos($str, ':') !== false || strpos($str, '|') !== false;
    }
}
