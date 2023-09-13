<?php

namespace Playcat\Queue\Driver;

use Playcat\Queue\Exceptions\ParamsError;
use Playcat\Queue\Protocols\ConsumerData;
use Playcat\Queue\Protocols\ConsumerDataInterface;
use Playcat\Queue\Protocols\DriverInterface;
use Playcat\Queue\Protocols\ProducerDataInterface;
use RuntimeException;

class Kafka extends Base implements DriverInterface
{

    public const CONSUMERGROUPNAME = 'PLAYCATCONSUMERGROUP';
    private $kafka_consumer;
    private $kafka_producer;
    private $config;

    public function __construct(array $config)
    {
        if (!extension_loaded('rdKafka')) {
            throw new RuntimeException('Please make sure the PHP RdKafka extension is installed and enabled.');
        }
        $this->config = new \RdKafka\Conf();
        $this->config->set('group.id', self::CONSUMERGROUPNAME);
        $this->config->set('metadata.broker.list', $config['host']);
        $this->config->set('auto.offset.reset', 'earliest');
        $this->config->set('enable.partition.eof', 'true');
    }

    /**
     * @return \RdKafka\KafkaConsumer
     */
    private function getKafkaConsumer(): \RdKafka\KafkaConsumer
    {
        if (!$this->kafka_consumer) {
            $this->kafka_consumer = new \RdKafka\KafkaConsumer($this->config);
        }
        return $this->kafka_consumer;
    }

    private function getKafkaProduce(): \RdKafka\Producer
    {
        if (!$this->kafka_producer) {
            $this->kafka_producer = new \RdKafka\Producer($this->config);
        }
        return $this->kafka_producer;
    }

    /**
     * @param array $channels
     * @return bool
     */
    public function subscribe(array $channels): bool
    {
        $this->getKafkaConsumer()->subscribe($channels);
        return true;
    }

    /**
     * @return ConsumerDataInterface|null
     * @throws ParamsError
     */
    public function shift(): ?ConsumerDataInterface
    {
        $result = null;
        $message = $this->getKafkaConsumer()->consume(0);
        switch ($message->err) {
            case RD_KAFKA_RESP_ERR_NO_ERROR:
                $msgid = $message->headers['message_id'];
                $result = new ConsumerData($message->payload);
                $result->setID($msgid);
                break;
            case RD_KAFKA_RESP_ERR__PARTITION_EOF:
            case RD_KAFKA_RESP_ERR__TIMED_OUT:
                break;
        }
        return $result;
    }

    /**
     * Remove it when done,
     * @return bool
     */
    public function consumerFinished(): bool
    {
        return (bool)$this->getKafkaConsumer()->commit();
    }

    /**
     * @param ProducerDataInterface $payload
     * @return string|null
     */
    public function push(ProducerDataInterface $payload): ?string
    {
        $msgid = $this->generateMsgid();
        $this->getKafkaProduce()->newTopic($payload->getChannel())->producev(RD_KAFKA_PARTITION_UA, 0, $payload->getJSON(), null, ['message_id' => $msgid]);
        return $this->getKafkaProduce()->flush(100) === RD_KAFKA_RESP_ERR_NO_ERROR
            ? $msgid : '';
    }

}
