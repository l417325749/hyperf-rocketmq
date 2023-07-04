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

class AckMessageRequest extends BaseRequest
{
    private $topicName;

    private $receiptHandles;

    private $consumer;

    private $trans;

    public function __construct($instanceId, $topicName, $consumer, array $receiptHandles)
    {
        parent::__construct($instanceId, 'delete', 'topics/' . $topicName . '/messages');

        $this->topicName = $topicName;
        $this->receiptHandles = $receiptHandles;
        $this->consumer = $consumer;
    }

    public function getTopicName()
    {
        return $this->topicName;
    }

    public function getReceiptHandles()
    {
        return $this->receiptHandles;
    }

    public function getConsumer()
    {
        return $this->consumer;
    }

    public function setTrans($trans)
    {
        $this->trans = $trans;
    }

    public function generateBody()
    {
        $xmlWriter = new \XMLWriter();
        $xmlWriter->openMemory();
        $xmlWriter->startDocument('1.0', 'UTF-8');
        $xmlWriter->startElementNS(null, Constants::RECEIPT_HANDLES, Constants::XML_NAMESPACE);
        foreach ($this->receiptHandles as $receiptHandle) {
            $xmlWriter->writeElement(Constants::RECEIPT_HANDLE, $receiptHandle);
        }
        $xmlWriter->endElement();
        $xmlWriter->endDocument();
        return $xmlWriter->outputMemory();
    }

    public function generateQueryString()
    {
        $params = ['consumer' => $this->consumer];
        if ($this->instanceId != null && $this->instanceId != '') {
            $params['ns'] = $this->instanceId;
        }
        if ($this->trans != null) {
            $params['trans'] = $this->trans;
        }
        return http_build_query($params);
    }
}
