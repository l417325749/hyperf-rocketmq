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

use Hyperf\RocketMQ\Aliyunmq\Model\FailResolveMessage;
use Hyperf\RocketMQ\Aliyunmq\Model\Message;
use Hyperf\RocketMQ\Aliyunmq\Model\MessagePartialResult;

class MessagePartialResolver
{
    public static function resolve($source)
    {
        $isMatched = preg_match_all('/<Message>[\s\S]*?<\/Message>/', $source, $matches);
        if (! $isMatched) {
            return null;
        }
        $messages = [];
        $failResolveMessages = [];
        foreach ($matches[0] as $match) {
            $message = null;
            try {
                $message = self::tryToResolveToMessage($match);
            } catch (\Exception $e) {
                $message = null;
            }
            if ($message === null) {
                $failResolveMessages[] = self::tryToConvertToFailResult($match);
            } else {
                $messages[] = $message;
            }
        }
        return new MessagePartialResult($messages, $failResolveMessages);
    }

    private static function tryToResolveToMessage($content)
    {
        $xmlReader = new \XMLReader();
        $isXml = $xmlReader->XML($content);
        if ($isXml === false) {
            return null;
        }
        $message = Message::fromXML($xmlReader);
        if ($message === null || $message->getMessageId() === null) {
            return null;
        }
        return $message;
    }

    private static function tryToConvertToFailResult($content)
    {
        $newContent = preg_replace('/(<MessageBody>[\s\S]*<\/MessageBody>)|(<Properties>[\s\S]*<\/Properties>)/', '', $content);
        if ($newContent === null) {
            return null;
        }
        $xmlReader = new \XMLReader();
        $isXml = $xmlReader->XML($newContent);
        if ($isXml === false) {
            return null;
        }
        $message = Message::fromXML($xmlReader);
        return new FailResolveMessage($message->getMessageId(), $message->getReceiptHandle(), $content);
    }
}
