<?php
/**
 * Basic integration test for the queue system without msgpack dependency
 */

namespace Playcat\Queue\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Playcat\Queue\Manager;
use Playcat\Queue\Producer\Producer;
use Playcat\Queue\Protocols\ProducerData;

class SimpleIntegrationTest extends TestCase
{
    /**
     * Test that Manager can be instantiated
     */
    public function testManagerInstantiation()
    {
        $manager = new Manager();
        $this->assertInstanceOf(Manager::class, $manager);
    }

    /**
     * Test that Producer can be instantiated
     */
    public function testProducerInstantiation()
    {
        $producer = new Producer();
        $this->assertInstanceOf(Producer::class, $producer);
    }

    /**
     * Test that ProducerData can be created and basic methods work
     */
    public function testProducerDataBasicMethods()
    {
        $producerData = new ProducerData();
        
        // Test ID
        $producerData->setID('test-id');
        $this->assertEquals('test-id', $producerData->getID());
        
        // Test Channel
        $producerData->setChannel('test-channel');
        $this->assertEquals('test-channel', $producerData->getChannel());
        
        // Test Retry Count
        $producerData->setRetryCount(3);
        $this->assertEquals(3, $producerData->getRetryCount());
        
        // Test Queue Data
        $testData = ['key' => 'value'];
        $producerData->setQueueData($testData);
        $this->assertEquals($testData, $producerData->getQueueData());
        
        // Test Delay Time
        $producerData->setDelayTime(60);
        $this->assertEquals(60, $producerData->getDelayTime());
    }
}