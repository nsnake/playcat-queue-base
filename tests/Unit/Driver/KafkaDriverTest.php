<?php

namespace Playcat\Queue\Tests\Unit\Driver;

use PHPUnit\Framework\TestCase;
use Playcat\Queue\Driver\Kafka;
use Playcat\Queue\Exceptions\ConnectFailExceptions;
use Mockery;

class KafkaDriverTest extends TestCase
{
    private $config;

    protected function setUp(): void
    {
        $this->config = [
            'brokers' => '127.0.0.1:9092',
            'topic' => 'test_topic',
            'group_id' => 'test_group',
            'timeout' => 5000,
        ];
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function testConstructorWithValidConfig()
    {
        $driver = new Kafka($this->config);
        $this->assertInstanceOf(Kafka::class, $driver);
    }

    public function testConstructorWithInvalidConfig()
    {
        $this->expectException(ConnectFailExceptions::class);

        $invalidConfig = [
            'brokers' => '',  // Empty broker list
            'topic' => 'test_topic'
        ];

        new Kafka($invalidConfig);
    }

    public function testPush()
    {
        $driver = new Kafka($this->config);
        $producerData = Mockery::mock('Playcat\Queue\Protocols\ProducerDataInterface');
        $producerData->shouldReceive('getData')->andReturn(['test' => 'data']);
        $producerData->shouldReceive('getQueueName')->andReturn('test_queue');

        $result = $driver->push($producerData);
        $this->assertTrue($result);
    }
}
