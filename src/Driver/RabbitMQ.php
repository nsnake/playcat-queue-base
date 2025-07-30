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

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Playcat\Queue\Exceptions\ConnectFailExceptions;
use Playcat\Queue\Exceptions\ParamsError;
use Playcat\Queue\Log\Log;
use Playcat\Queue\Protocols\ConsumerData;
use Playcat\Queue\Protocols\ConsumerDataInterface;
use Playcat\Queue\Protocols\ProducerDataInterface;
use RuntimeException;

class RabbitMQ extends Base implements DriverInterface
{

    public const CONSUMERGROUPNAME = 'PLAYCATCONSUMERGROUP';
    public const CONSUMEREXCHANGENAM = 'PLAYCATCONSUMERGEXCHANGE';
    private $config = [];
    private $del_msgid = [];

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
            $message = 'Connection to Rabbitmq failed.' . $e->getMessage();
            Log::emergency($message);
            throw new ConnectFailExceptions($message);
        }
        Log::info('Driver By RabbitMQ');
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
        $result = null;
        $result = $this->getConnection()->basic_get(self::CONSUMERGROUPNAME);
        if ($result) {
            $this->current_msg = $result;
            $msg_id = $this->current_msg->get('message_id');
            if (!in_array($msg_id, $this->del_msgid)) {
                $result = new ConsumerData($result->body);
                $result->setID($msg_id);
            }
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
        $data = new AMQPMessage($payload->serializeData(), ['message_id' => $msgid]);
        $this->getConnection()->basic_publish($data, self::CONSUMEREXCHANGENAM, $payload->getChannel());
        return $msgid;
    }

    /**
     * @param string $channel
     * @return bool
     */
    public function flush(string $channel): bool
    {
        $this->getConnection()->queue_unbind(self::CONSUMERGROUPNAME, self::CONSUMEREXCHANGENAM, $channel);
        $this->getConnection()->queue_delete(self::CONSUMERGROUPNAME);
        $this->getConnection()->queue_declare(self::CONSUMERGROUPNAME, false, true, false, false);
        $this->getConnection()->queue_bind(self::CONSUMERGROUPNAME, self::CONSUMEREXCHANGENAM, $channel);
        return true;
    }

    /**
     * @param string $channel
     * @param array $ids
     * @return bool
     */
    public function del(string $channel, array $ids): bool
    {
        $this->del_msgid = array_merge($this->del_msgid, $ids);
        return true;
    }

}
