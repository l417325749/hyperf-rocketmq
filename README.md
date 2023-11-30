## 引入Composer包

```
composer require lpb/hyperf-rocketmq
```
## 发布配置文件
```
php bin/hyperf.php vendor:publish lpb/hyperf-rocketmq
```


## 新建生产者

```php
#[Producer(poolName: "default", topic: "test_topic", groupId: "test_group", messageTag: "tMsgKey", properties: ["a" => 1])]
class DemoProducer extends ProducerMessage
{
    public function __construct(array $data)
    {
        // 设置消息内容
        $this->setPayload($data);
    }

}
```

## 新建消费者

```php
#[Consumer(name: "Consumer", poolName: "default", topic: "test_topic", groupId: "test_group", messageTag: "tMsgKey")]
class DemoConsumer extends ConsumerMessage
{
    public function consumeMessage(RocketMQMessage $message): void
    {
        var_dump($message->getMessageId());
        var_dump($this->unserialize($message->getMessageBody()));
        var_dump($message->getPublishTime());
    }
}
```