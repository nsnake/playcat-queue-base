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
use Playcat\Queue\Log\Log;
use Playcat\Queue\Model\Payload;
use Playcat\Queue\Protocols\ConsumerData;
use Playcat\Queue\Protocols\ConsumerDataInterface;
use Playcat\Queue\Protocols\ProducerDataInterface;
use RedisException;
use RuntimeException;

class Redis extends Base implements DriverInterface
{
    public const CONSUMERGROUPNAME = 'PLAYCATCONSUMERGROUP';
    protected $channels = [];
    private $redis;
    private $current_id = 0;
    private $current_channel = 0;
    private $config;

    public function __construct(array $config)
    {
        if (!extension_loaded('redis')) {
            $message = 'Can not load Redis extension, Please make sure the PHP Redis extension is installed and enabled.';
            Log::emergency($message);
            throw new RuntimeException($message);
        }
        $this->config = $config;
        $this->connectRedis();
        Log::info('Driver By Redis');
    }


    /**
     * @return void
     */
    private function connectRedis(): void
    {
        $this->redis = new \Redis();
        $address = $this->config['host'];
        $this->redis->connect(parse_url($address, PHP_URL_HOST), parse_url($address, PHP_URL_PORT), $this->config['options']['timeout'] ?? 3);
        if ($this->config['options']['auth']) {
            $this->redis->auth($this->config['options']['auth']);
        }
        $this->redis->select($this->config['options']['db'] ?? 0);
    }

    /**
     * @return \Redis
     */
    private function getRedis(): \Redis
    {
        try {
            if (!$this->redis || !$this->redis->ping()) {
                $this->connectRedis();
            }
        } catch (RedisException $e) {
        }
        return $this->redis;
    }

    /**
     * @param array $channels
     * @return bool
     */
    public function subscribe(array $channels): bool
    {
        $result = true;
        foreach ($channels as $channel) {
            $this->channels[$channel] = '>';
            if (
                !$this->getRedis()
                    ->xGroup('CREATE', $channel, self::CONSUMERGROUPNAME, '0', true)
            ) {
                $result = false;
            }
        }
        return $result;
    }

    /**
     * @return ConsumerDataInterface|null
     * @throws ParamsError
     */
    public function shift(): ?ConsumerDataInterface
    {
        $result = $this->getRedis()
            ->xReadGroup(self::CONSUMERGROUPNAME, "consumer_" . $this->iconic_id, $this->channels, 1);
        if ($result) {
            $this->current_channel = key($result);
            $this->current_id = key($result[$this->current_channel]);
            $result = new ConsumerData($result[$this->current_channel][$this->current_id]);
            $result->setID($this->current_id);
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
        return $this->getRedis()->xAck($this->current_channel, self::CONSUMERGROUPNAME, [$this->current_id]);
    }

    /**
     * @param ProducerDataInterface $payload
     * @return string|null
     */
    public function push(ProducerDataInterface $payload): ?string
    {
        return $this->getRedis()->xadd($payload->getChannel(), '*', $payload->serializeData(true));
    }

    /**
     * Flush the queue safely
     * @param string $channel
     * @return int|bool
     */
    public function flush(string $channel): int|bool
    {
        return $this->getRedis()->xtrim($channel, 'MAXLEN', 0);
    }

    /**
     * Delete data if it hasn't already been done
     * @param string $channel
     * @param array $ids
     * @return int|bool
     */
    public function del(string $channel, array $ids): int|bool
    {
        return $this->getRedis()->xdel($channel, $ids);
    }
}
