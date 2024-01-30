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

namespace Playcat\Queue\Producer;

use Playcat\Queue\Driver\DriverInterface;
use Playcat\Queue\Driver\Kafka;
use Playcat\Queue\Driver\RabbitMQ;
use Playcat\Queue\Driver\Redis;
use Playcat\Queue\Driver\Rediscluster;

class Producer
{
    /**
     * @param array $manager_config
     * @return DriverInterface
     * @throws \Playcat\Queue\Exceptions\ConnectFailExceptions
     */
    public function DriverByAuto(array $manager_config): DriverInterface
    {
        switch ($manager_config['driver']) {
            case 'Playcat\Queue\Driver\Rediscluster':
                $driver = $this->DriverByRediscluster($manager_config['Rediscluster']);
                break;
            case 'Playcat\Queue\Driver\Kafka':
                $driver = $this->DriverByKafka($manager_config['Kafka']);
                break;
            case 'Playcat\Queue\Driver\RabbitMQ':
                $driver = $this->DriverByRabbitmq($manager_config['Rabbitmq']);
                break;
            default:
                $driver = $this->DriverByRedis($manager_config['Redis']);
        }
        return $driver;
    }

    /**
     * @param array $redis_config
     * @return Redis
     */
    public function DriverByRedis(array $redis_config): Redis
    {
        return new Redis($redis_config);
    }

    /**
     * @param array $rediscluster_config
     * @return Rediscluster
     */
    public function DriverByRediscluster(array $rediscluster_config): Rediscluster
    {
        return new Rediscluster($rediscluster_config);
    }

    /**
     * @param array $kafka_config
     * @return Kafka
     */
    public function DriverByKafka(array $kafka_config): Kafka
    {
        return new Kafka($kafka_config);
    }

    /**
     * @param array $rabbitmq_config
     * @return RabbitMQ
     * @throws \Playcat\Queue\Exceptions\ConnectFailExceptions
     */
    public function DriverByRabbitmq(array $rabbitmq_config): RabbitMQ
    {
        return new RabbitMQ($rabbitmq_config);
    }
}
