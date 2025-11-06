<?php

namespace Playcat\Queue\Tests\Unit\Driver;

use PHPUnit\Framework\TestCase;
use Playcat\Queue\Driver\RabbitMQ;
use Playcat\Queue\Exceptions\ConnectFailExceptions;
use Mockery;

class RabbitMQDriverTest extends TestCase
{
    private $config;

    protected function setUp(): void
    {
        $this->config = [
            'host' => '127.0.0.1',
            'port' => 5672,
            'user' => 'guest',
            'password' => 'guest',
            'vhost' => '/',
        ];
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function testConstructorWithValidConfig()
    {
        $driver = new RabbitMQ($this->config);
        $this->assertInstanceOf(RabbitMQ::class, $driver);
    }

    public function testConstructorWithInvalidConfig()
    {
        $this->expectException(ConnectFailExceptions::class);

        $invalidConfig = [
            'host' => '127.0.0.1',
            'port' => 5673, // Wrong port
            'user' => 'guest',
            'password' => 'guest',
        ];

        new RabbitMQ($invalidConfig);
    }

    public function testPushMessage()
    {
        $driver = new RabbitMQ($this->config);
        $producerData = Mockery::mock('Playcat\Queue\Protocols\ProducerDataInterface');
        $producerData->shouldReceive('getData')->andReturn(['test' => 'data']);
        $producerData->shouldReceive('getQueueName')->andReturn('test_queue');
        $producerData->shouldReceive('getDelay')->andReturn(0);

        $result = $driver->push($producerData);
        $this->assertTrue($result);
    }

    public function testPushDelayedMessage()
    {
        $driver = new RabbitMQ($this->config);
        $producerData = Mockery::mock('Playcat\Queue\Protocols\ProducerDataInterface');
        $producerData->shouldReceive('getData')->andReturn(['test' => 'data']);
        $producerData->shouldReceive('getQueueName')->andReturn('test_queue');
        $producerData->shouldReceive('getDelay')->andReturn(60);

        $result = $driver->push($producerData);
        $this->assertTrue($result);
    }
}
