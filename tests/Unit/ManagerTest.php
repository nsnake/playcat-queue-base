<?php

namespace Playcat\Queue\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Playcat\Queue\Manager;
use Playcat\Queue\Driver\Redis;
use Playcat\Queue\Driver\Kafka;
use Playcat\Queue\Driver\RabbitMQ;
use Mockery;

class ManagerTest extends TestCase
{
    private $container;
    private $config;

    protected function setUp(): void
    {
        $this->container = Mockery::mock('Psr\Container\ContainerInterface');
        $this->config = [
            'default' => 'redis',
            'drivers' => [
                'redis' => [
                    'driver' => 'redis',
                    'host' => '127.0.0.1',
                    'port' => 6379,
                ],
                'kafka' => [
                    'driver' => 'kafka',
                    'brokers' => '127.0.0.1:9092',
                ],
                'rabbitmq' => [
                    'driver' => 'rabbitmq',
                    'host' => '127.0.0.1',
                    'port' => 5672,
                ],
            ],
        ];
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function testCreateRedisDriver()
    {
        $manager = new Manager($this->container, $this->config);
        $driver = $manager->driver('redis');
        $this->assertInstanceOf(Redis::class, $driver);
    }

    public function testCreateKafkaDriver()
    {
        $manager = new Manager($this->container, $this->config);
        $driver = $manager->driver('kafka');
        $this->assertInstanceOf(Kafka::class, $driver);
    }

    public function testCreateRabbitMQDriver()
    {
        $manager = new Manager($this->container, $this->config);
        $driver = $manager->driver('rabbitmq');
        $this->assertInstanceOf(RabbitMQ::class, $driver);
    }

    public function testDefaultDriver()
    {
        $manager = new Manager($this->container, $this->config);
        $driver = $manager->driver();
        $this->assertInstanceOf(Redis::class, $driver);
    }

    public function testInvalidDriver()
    {
        $this->expectException(\InvalidArgumentException::class);

        $manager = new Manager($this->container, $this->config);
        $manager->driver('invalid_driver');
    }
}
