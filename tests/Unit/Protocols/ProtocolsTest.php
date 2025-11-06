<?php

namespace Playcat\Queue\Tests\Unit\Protocols;

use PHPUnit\Framework\TestCase;
use Playcat\Queue\Protocols\ProducerData;
use Playcat\Queue\Protocols\ConsumerData;

class ProtocolsTest extends TestCase
{
    public function testProducerDataSettersAndGetters()
    {
        $producerData = new ProducerData();

        $queueName = 'test_queue';
        $data = ['message' => 'test'];
        $delay = 60;

        $producerData->setQueueName($queueName);
        $producerData->setData($data);
        $producerData->setDelay($delay);

        $this->assertEquals($queueName, $producerData->getQueueName());
        $this->assertEquals($data, $producerData->getData());
        $this->assertEquals($delay, $producerData->getDelay());
    }

    public function testConsumerDataSettersAndGetters()
    {
        $consumerData = new ConsumerData();

        $queueName = 'test_queue';
        $data = ['message' => 'test'];
        $messageId = 'msg_123';

        $consumerData->setQueueName($queueName);
        $consumerData->setData($data);
        $consumerData->setMessageId($messageId);

        $this->assertEquals($queueName, $consumerData->getQueueName());
        $this->assertEquals($data, $consumerData->getData());
        $this->assertEquals($messageId, $consumerData->getMessageId());
    }

    public function testProducerDataToArray()
    {
        $producerData = new ProducerData();
        $data = [
            'queue_name' => 'test_queue',
            'data' => ['message' => 'test'],
            'delay' => 60
        ];

        $producerData->setQueueName($data['queue_name']);
        $producerData->setData($data['data']);
        $producerData->setDelay($data['delay']);

        $this->assertEquals($data, $producerData->toArray());
    }
}
