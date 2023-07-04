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
namespace Hyperf\RocketMQ\Aliyunmq\Model;

use Hyperf\RocketMQ\Aliyunmq\Constants;
use Hyperf\RocketMQ\Aliyunmq\Traits\MessagePropertiesForConsume;

class Message
{
    use MessagePropertiesForConsume;

    public function __construct(
        $messageId,
        $messageBodyMD5,
        $messageBody,
        $publishTime,
        $nextConsumeTime,
        $firstConsumeTime,
        $consumedTimes,
        $receiptHandle,
        $messageTag,
        $properties
    ) {
        $this->messageId = $messageId;
        $this->messageBodyMD5 = $messageBodyMD5;
        $this->messageBody = $messageBody;
        $this->publishTime = $publishTime;
        $this->nextConsumeTime = $nextConsumeTime;
        $this->firstConsumeTime = $firstConsumeTime;
        $this->consumedTimes = $consumedTimes;
        $this->receiptHandle = $receiptHandle;
        $this->messageTag = $messageTag;
        $this->properties = $properties;
    }

    public static function fromXML(\XMLReader $xmlReader)
    {
        $messageId = null;
        $messageBodyMD5 = null;
        $messageBody = null;
        $publishTime = null;
        $nextConsumeTime = null;
        $firstConsumeTime = null;
        $consumedTimes = null;
        $receiptHandle = null;
        $messageTag = null;
        $properties = null;

        while ($xmlReader->read()) {
            switch ($xmlReader->nodeType) {
                case \XMLReader::ELEMENT:
                    switch ($xmlReader->name) {
                        case Constants::MESSAGE_ID:
                            $xmlReader->read();
                            if ($xmlReader->nodeType == \XMLReader::TEXT) {
                                $messageId = $xmlReader->value;
                            }
                            break;
                        case Constants::MESSAGE_BODY_MD5:
                            $xmlReader->read();
                            if ($xmlReader->nodeType == \XMLReader::TEXT) {
                                $messageBodyMD5 = $xmlReader->value;
                            }
                            break;
                        case Constants::MESSAGE_BODY:
                            $xmlReader->read();
                            if ($xmlReader->nodeType == \XMLReader::TEXT) {
                                $messageBody = $xmlReader->value;
                            }
                            break;
                        case Constants::PUBLISH_TIME:
                            $xmlReader->read();
                            if ($xmlReader->nodeType == \XMLReader::TEXT) {
                                $publishTime = $xmlReader->value;
                            }
                            break;
                        case Constants::NEXT_CONSUME_TIME:
                            $xmlReader->read();
                            if ($xmlReader->nodeType == \XMLReader::TEXT) {
                                $nextConsumeTime = $xmlReader->value;
                            }
                            break;
                        case Constants::FIRST_CONSUME_TIME:
                            $xmlReader->read();
                            if ($xmlReader->nodeType == \XMLReader::TEXT) {
                                $firstConsumeTime = $xmlReader->value;
                            }
                            break;
                        case Constants::CONSUMED_TIMES:
                            $xmlReader->read();
                            if ($xmlReader->nodeType == \XMLReader::TEXT) {
                                $consumedTimes = $xmlReader->value;
                            }
                            break;
                        case Constants::RECEIPT_HANDLE:
                            $xmlReader->read();
                            if ($xmlReader->nodeType == \XMLReader::TEXT) {
                                $receiptHandle = $xmlReader->value;
                            }
                            break;
                        case Constants::MESSAGE_TAG:
                            $xmlReader->read();
                            if ($xmlReader->nodeType == \XMLReader::TEXT) {
                                $messageTag = $xmlReader->value;
                            }
                            break;
                        case Constants::MESSAGE_PROPERTIES:
                            $xmlReader->read();
                            if ($xmlReader->nodeType == \XMLReader::TEXT) {
                                $propertiesString = $xmlReader->value;
                                if ($propertiesString != null) {
                                    $kvArray = explode('|', $propertiesString);
                                    foreach ($kvArray as $kv) {
                                        $kAndV = explode(':', $kv);
                                        if (sizeof($kAndV) == 2) {
                                            $properties[$kAndV[0]] = $kAndV[1];
                                        }
                                    }
                                }
                            }
                            break;
                    }
                    break;
                case \XMLReader::END_ELEMENT:
                    if ($xmlReader->name == 'Message') {
                        return new Message(
                            $messageId,
                            $messageBodyMD5,
                            $messageBody,
                            $publishTime,
                            $nextConsumeTime,
                            $firstConsumeTime,
                            $consumedTimes,
                            $receiptHandle,
                            $messageTag,
                            $properties
                        );
                    }
                    break;
            }
        }

        return new Message(
            $messageId,
            $messageBodyMD5,
            $messageBody,
            $publishTime,
            $nextConsumeTime,
            $firstConsumeTime,
            $consumedTimes,
            $receiptHandle,
            $messageTag,
            $properties
        );
    }
}
