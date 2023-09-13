<?php

namespace Playcat\Queue\Driver;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Playcat\Queue\Exceptions\ConnectFailExceptions;
use Playcat\Queue\Exceptions\ParamsError;
use Playcat\Queue\Protocols\ConsumerData;
use Playcat\Queue\Protocols\ConsumerDataInterface;
use Playcat\Queue\Protocols\DriverInterface;
use Playcat\Queue\Protocols\ProducerDataInterface;
use RuntimeException;

class RabbitMQ extends Base implements DriverInterface
{

    public const CONSUMERGROUPNAME = 'PLAYCATCONSUMERGROUP';
    public const CONSUMEREXCHANGENAM = 'PLAYCATCONSUMERGEXCHANGE';
    private $config = [];

    /**
     * @var AMQPStreamConnection
     */
    private $connection;
    private $current_msg;
    /**
     * @var \PhpAmqpLib\Channel\AbstractChannel|\PhpAmqpLib\Channel\AMQPChannel
     */
    private $rabbitmq;

    /**
     * @param string $config_name
     * @throws ConnectFailExceptions
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        try {
            $this->connection = new AMQPStreamConnection(
                parse_url($this->config['host'], PHP_URL_HOST),
                parse_url($this->config['host'], PHP_URL_PORT),
                $this->config['options']['user'] ?? 'guest',
                $this->config['options']['password'] ?? 'guest',
                $this->config['options']['vhost'] ?? '/'
            );
        } catch (\Exception $e) {
            throw new ConnectFailExceptions('Connection to rabbitmq failed.' . $e->getMessage());
        }
    }

    /**
     * @return AMQPChannel
     */
    private function getConnection(): AMQPChannel
    {
        if (!$this->rabbitmq) {
            $this->rabbitmq = $this->connection->channel();
            $this->rabbitmq->exchange_declare(self::CONSUMEREXCHANGENAM, 'topic', false, true, false);
            $this->rabbitmq->queue_declare(self::CONSUMERGROUPNAME, false, true, false, false);
        }
        return $this->rabbitmq;
    }


    /**
     * @param array $channels
     * @return bool
     */
    public function subscribe(array $channels): bool
    {

        foreach ($channels as $channel) {
            $this->getConnection()->queue_bind(self::CONSUMERGROUPNAME, self::CONSUMEREXCHANGENAM, $channel);
        }
        return true;

    }

    /**
     * @return ConsumerDataInterface|null
     * @throws ParamsError
     */
    public function shift(): ?ConsumerDataInterface
    {
        $result = $this->getConnection()->basic_get(self::CONSUMERGROUPNAME);
        if ($result) {
            $this->current_msg = $result;
            $result = new ConsumerData($result->body);
            $result->setID($this->current_msg->get('message_id'));
        } else {
            $result = null;
        }
        return $result;
    }

    /**
     * Remove it when done,
     * @return bool
     */
    public function consumerFinished(): bool
    {
        $this->getConnection()->basic_ack($this->current_msg->delivery_info['delivery_tag']);
        return true;
    }

    /**
     * @param ProducerDataInterface $payload
     * @return string|null
     */
    public function push(ProducerDataInterface $payload): ?string
    {
        $msgid = $this->generateMsgid();
        $data = new AMQPMessage($payload->getJSON(), ['message_id' => $msgid]);
        $this->getConnection()->basic_publish($data, self::CONSUMEREXCHANGENAM, $payload->getChannel());
        return $msgid;
    }
}
