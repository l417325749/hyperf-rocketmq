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

use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;

class MQPromise
{
    private $response;

    private $promise;

    public function __construct(PromiseInterface &$promise, BaseResponse &$response)
    {
        $this->promise = $promise;
        $this->response = $response;
    }

    public function isCompleted()
    {
        return $this->promise->getState() != 'pending';
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function getState()
    {
        return $this->promise->getState();
    }

    public function wait()
    {
        try {
            $res = $this->promise->wait();
            if ($res instanceof ResponseInterface) {
                $this->response->setRequestId($res->getHeaderLine('x-mq-request-id'));
                return $this->response->parseResponse($res->getStatusCode(), $res->getBody());
            }
        } catch (TransferException $e) {
            $this->response->parseErrorResponse($e->getCode(), $e->getMessage());
        }
        $this->response->parseErrorResponse('500', 'Unknown');
    }
}
