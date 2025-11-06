<?php

namespace Playcat\Queue\Tests\Unit\Producer;

use PHPUnit\Framework\TestCase;
use Playcat\Queue\Producer\Producer;
use Playcat\Queue\Protocols\ProducerData;
use Mockery;

class ProducerTest extends TestCase
{
    private $producer;
    private $mockDriver;

    protected function setUp(): void
    {
        $this->mockDriver = Mockery::mock('Playcat\Queue\Driver\DriverInterface');
        $this->producer = new Producer($this->mockDriver);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function testPublishMessage()
    {
        $data = new ProducerData();
        $data->setQueueName('test_queue');
        $data->setData(['message' => 'test']);

        $this->mockDriver->shouldReceive('push')
            ->once()
            ->with($data)
            ->andReturn(true);

        $result = $this->producer->publish($data);
        $this->assertTrue($result);
    }

    public function testPublishDelayedMessage()
    {
        $data = new ProducerData();
        $data->setQueueName('test_queue');
        $data->setData(['message' => 'test']);
        $data->setDelay(60);

        $this->mockDriver->shouldReceive('push')
            ->once()
            ->with($data)
            ->andReturn(true);

        $result = $this->producer->publish($data);
        $this->assertTrue($result);
    }
}
