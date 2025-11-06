<?php

namespace Playcat\Queue\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Playcat\Queue\Manager;
use Playcat\Queue\Producer\Producer;
use Playcat\Queue\Protocols\ProducerData;

/**
 * Basic integration test for Playcat Queue
 */
class BasicIntegrationTest extends TestCase
{
    /**
     * Test basic manager instantiation
     */
    public function testManagerCanBeInstantiated()
    {
        $manager = Manager::getInstance();
        $this->assertInstanceOf(Manager::class, $manager);
    }

    /**
     * Test producer instantiation
     */
    public function testProducerCanBeInstantiated()
    {
        $producer = new Producer();
        $this->assertInstanceOf(Producer::class, $producer);
    }

    /**
     * Test producer data creation
     */
    public function testProducerDataCreation()
    {
        $producerData = new ProducerData();
        $producerData->setID('test-id-123');
        $producerData->setChannel('test-channel');
        $producerData->setQueueData(['message' => 'Hello World']);
        $producerData->setDelayTime(0);
        $producerData->setRetryCount(3);

        $this->assertEquals('test-id-123', $producerData->getID());
        $this->assertEquals('test-channel', $producerData->getChannel());
        $this->assertEquals(['message' => 'Hello World'], $producerData->getQueueData());
        $this->assertEquals(0, $producerData->getDelayTime());
        $this->assertEquals(3, $producerData->getRetryCount());
    }

    /**
     * Test producer data serialization (skipped due to msgpack dependency)
     */
    public function testProducerDataSerialization()
    {
        // Skip this test if msgpack extension is not available
        if (!function_exists('msgpack_pack')) {
            $this->markTestSkipped('msgpack extension not available');
            return;
        }
        
        $producerData = new ProducerData();
        $producerData->setID('test-id-456');
        $producerData->setChannel('test-channel');
        $producerData->setQueueData(['key' => 'value']);
        $producerData->setDelayTime(0);
        $producerData->setRetryCount(1);

        $serialized = $producerData->serializeData(false);
        $this->assertIsString($serialized);
        
        $serializedRedis = $producerData->serializeData(true);
        $this->assertIsArray($serializedRedis);
    }
}