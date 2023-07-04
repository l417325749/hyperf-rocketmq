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
namespace Hyperf\RocketMQ\Aliyunmq;

class Constants
{
    public const GMT_DATE_FORMAT = 'D, d M Y H:i:s \\G\\M\\T';

    public const CLIENT_VERSION = 'mq-php-sdk/1.0.4(GuzzleHttp/';

    public const VERSION_HEADER = 'x-mq-version';

    public const HEADER_PREFIX = 'x-mq-';

    public const XML_NAMESPACE = 'http://mq.aliyuncs.com/doc/v1/';

    public const VERSION_VALUE = '2015-06-06';

    public const AUTHORIZATION = 'Authorization';

    public const AUTH_PREFIX = 'MQ';

    public const CONTENT_LENGTH = 'Content-Length';

    public const CONTENT_TYPE = 'Content-Type';

    public const SECURITY_TOKEN = 'security-token';

    public const USER_AGENT = 'User-Agent';

    // XML Tag
    public const ERROR = 'Error';

    public const ERRORS = 'Errors';

    public const MESSAGE_BODY = 'MessageBody';

    public const MESSAGE_TAG = 'MessageTag';

    public const MESSAGE_PROPERTIES = 'Properties';

    public const MESSAGE_ID = 'MessageId';

    public const MESSAGE_BODY_MD5 = 'MessageBodyMD5';

    public const PUBLISH_TIME = 'PublishTime';

    public const NEXT_CONSUME_TIME = 'NextConsumeTime';

    public const FIRST_CONSUME_TIME = 'FirstConsumeTime';

    public const RECEIPT_HANDLE = 'ReceiptHandle';

    public const RECEIPT_HANDLES = 'ReceiptHandles';

    public const CONSUMED_TIMES = 'ConsumedTimes';

    public const ERROR_CODE = 'ErrorCode';

    public const ERROR_MESSAGE = 'ErrorMessage';

    // some ErrorCodes
    public const INVALID_ARGUMENT = 'InvalidArgument';

    public const MALFORMED_XML = 'MalformedXML';

    public const MESSAGE_NOT_EXIST = 'MessageNotExist';

    public const RECEIPT_HANDLE_ERROR = 'ReceiptHandleError';

    public const ACK_FAIL = 'AckFail';

    public const TOPIC_NOT_EXIST = 'TopicNotExist';

    public const MESSAGE_PROPERTIES_MSG_KEY = 'KEYS';

    public const MESSAGE_PROPERTIES_TRANS_CHECK_KEY = '__TransCheckT';

    public const MESSAGE_PROPERTIES_TIMER_KEY = '__STARTDELIVERTIME';

    public const MESSAGE_PROPERTIES_SHARDING = '__SHARDINGKEY';

    public const TRANSACTION_ROLLBACK = 'rollback';

    public const TRANSACTION_COMMIT = 'commit';

    public const TRANSACTION_POP = 'pop';

    public const TRANSACTION_ORDER = 'order';
}
