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
namespace Hyperf\RocketMQ\Aliyunmq\Requests;

use Hyperf\RocketMQ\Aliyunmq\Constants;
use Hyperf\RocketMQ\Aliyunmq\Traits\MessagePropertiesForPublish;

class PublishMessageRequest extends BaseRequest
{
    use MessagePropertiesForPublish;

    private $topicName;

    public function __construct($instanceId, $topicName, $messageBody, $properties = null, $messageTag = null)
    {
        parent::__construct($instanceId, 'post', 'topics/' . $topicName . '/messages');

        $this->topicName = $topicName;
        $this->messageBody = $messageBody;
        $this->messageTag = $messageTag;
        $this->properties = $properties;
    }

    public function getTopicName()
    {
        return $this->topicName;
    }

    public function generateBody()
    {
        $xmlWriter = new \XMLWriter();
        $xmlWriter->openMemory();
        $xmlWriter->startDocument('1.0', 'UTF-8');
        $xmlWriter->startElementNS(null, 'Message', Constants::XML_NAMESPACE);
        $this->writeMessagePropertiesForPublishXML($xmlWriter);
        $xmlWriter->endElement();
        $xmlWriter->endDocument();
        return $xmlWriter->outputMemory();
    }

    public function generateQueryString()
    {
        if ($this->instanceId != null && $this->instanceId != '') {
            return http_build_query(['ns' => $this->instanceId]);
        }
        return null;
    }
}
