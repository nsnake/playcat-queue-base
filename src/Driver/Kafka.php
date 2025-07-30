<?php
/**
 *
 *
 * @license MIT License (MIT)
 *
 * For full copyright and license information, please see the LICENCE files.
 *
 * @author CGI.NET
 */

namespace Playcat\Queue\Driver;

use Playcat\Queue\Exceptions\ParamsError;
use Playcat\Queue\Protocols\ConsumerData;
use Playcat\Queue\Protocols\ConsumerDataInterface;
use Playcat\Queue\Protocols\ProducerDataInterface;
use RuntimeException;
use Playcat\Queue\Log\Log;

class Kafka extends Base implements DriverInterface
{

    public const CONSUMERGROUPNAME = 'PLAYCATCONSUMERGROUP';
    private $kafka_consumer;
    private $kafka_producer;
    private $config;

    public function __construct(array $config)
    {
        if (!extension_loaded('rdKafka')) {
            $message = 'Can not load Kafka extension, Please make sure the PHP RdKafka extension is installed and enabled.';
            Log::emergency($message);
            throw new RuntimeException($message);
        }
        $this->config = new \RdKafka\Conf();
        $this->config->set('group.id', self::CONSUMERGROUPNAME);
        $this->config->set('metadata.broker.list', $config['host']);
        $this->config->set('auto.offset.reset', 'earliest');
        $this->config->set('enable.partition.eof', 'true');
        Log::info('Driver By Kafka');
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
                //Messages that have been deleted are not processed
                if (!empty($message->payload)) {
                    $result = new ConsumerData($message->payload);
                    $result->setID($msgid);
                }
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
        $this->getKafkaProduce()
            ->newTopic($payload->getChannel())
            ->producev(RD_KAFKA_PARTITION_UA, 0, $payload->serializeData(), null, ['message_id' => $msgid]);
        return $this->getKafkaProduce()->flush(500) === RD_KAFKA_RESP_ERR_NO_ERROR
            ? $msgid : '';
    }

    /**
     * @param string $queue
     * @return int|bool
     */
    public function flush(string $channel): bool
    {
        return (new \RdKafka\AdminClient($this->config))
            ->deleteTopic([$queue], 5000);
    }


    /**
     * @param string $channel
     * @param array $ids
     * @return int
     */
    public function del(string $channel, array $ids): int
    {
        $result = 0;
        foreach ($ids as $msgid) {
            $this->getKafkaProduce()
                ->newTopic($payload->getChannel())
                ->producev(RD_KAFKA_PARTITION_UA, 0, '', null, ['message_id' => $msgid]);
            if ($this->getKafkaProduce()->flush(500) === RD_KAFKA_RESP_ERR_NO_ERROR) {
                $result++;
            }
        }
        return $result;
    }
}
