# playcat-queue-base

### 添加任务并且提交到队列中

```php
use Playcat\Queue\Manager;
use Playcat\Queue\Protocols\ProducerData;

  $manager_config = [
        'driver' => \Playcat\Queue\Driver\Redis::class,
        'timerserver' => '127.0.0.1:6678',
        'Redis' => ['host' => '127.0.0.1:6379']
        ];
  $payload = new ProducerData();
  //对应消费队列里的任务名称
  $payload->setChannel('test');
  //对应消费队列里的任务使用的数据
  $payload->setQueueData([1,2,3,4]);
  //推入队列并且获取消息id
  $id = Manager::getInstance()
        ->setConf($manager_config)
        ->push($payload);

  //延迟消费消息
  $payload_delay = new ProducerData();
  $payload_delay->setChannel('test');
  $payload_delay->setQueueData([6,7,8,9]);
  //设置60秒后执行的任务
  $payload_delay->setDelayTime(60);
  //推入队列并且获取消息id
  $id = Manager::getInstance()
        ->setConf($manager_config)
        ->push($payload_delay);
  //取消延迟消息
  Manager::getInstance()->del($id);

```

### ProducerData方法

- setChannel: 设置推入消息的队列名称
- setQueueData: 设置传入到消费任务的数据
- setDelayTime: 设置延迟时间(秒)
- - -

### 更多

基于tp和swoole的队列系统
[playcat-queue-tpswoole](https://github.com/nsnake/playcat-queue-tpswoole)

基于webman的队列系统
[playcat-queue-webman](https://github.com/nsnake/playcat-queue-webman)

### 联系
QQ:318274085

## License

MIT