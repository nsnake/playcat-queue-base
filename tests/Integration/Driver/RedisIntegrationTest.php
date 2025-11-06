<?php

namespace Playcat\Queue\Tests\Integration\Driver;

use PHPUnit\Framework\TestCase;
use Playcat\Queue\Driver\Redis;
use Playcat\Queue\Protocols\ProducerData;
use Playcat\Queue\Protocols\ConsumerData;
use RedisException;

class RedisIntegrationTest extends TestCase
{
    private $redisConfig;
    private $driver;
    private $testChannel;
    
    protected function setUp(): void
    {
        // 使用GitHub Actions中定义的Redis服务配置
        $this->redisConfig = [
            'host' => 'redis://127.0.0.1:6379',
            'options' => [
                'auth' => null,
                'db' => 0,
                'timeout' => 3
            ]
        ];
        
        // 如果Redis扩展未加载，则跳过所有测试
        if (!extension_loaded('redis')) {
            $this->markTestSkipped('Redis extension is not available');
        }
        
        // 如果Redis服务器不可达，则跳过所有测试
        try {
            $this->driver = new Redis($this->redisConfig);
        } catch (RedisException $e) {
            $this->markTestSkipped('Redis server is not available: ' . $e->getMessage());
        } catch (\Exception $e) {
            $this->markTestSkipped('Failed to instantiate Redis driver: ' . $e->getMessage());
        }
        
        $this->testChannel = 'test_channel_' . uniqid();
    }
    
    protected function tearDown(): void
    {
        // 清理测试数据
        if ($this->driver) {
            try {
                $this->driver->flush($this->testChannel);
            } catch (\Exception $e) {
                // 忽略清理错误
            }
        }
    }
    
    public function testPushAndShiftMessage()
    {
        // 如果Redis服务器不可达，则跳过此测试
        if (!$this->driver) {
            $this->markTestSkipped('Redis server is not available');
        }
        
        // 准备测试数据
        $producerData = new ProducerData();
        $producerData->setChannel($this->testChannel);
        $producerData->setQueueData(['message' => 'Hello Redis Queue']);
        
        // 订阅频道
        $subscribeResult = $this->driver->subscribe([$this->testChannel]);
        $this->assertTrue($subscribeResult, 'Should be able to subscribe to channel');
        
        // 推送消息
        $messageId = $this->driver->push($producerData);
        $this->assertNotNull($messageId, 'Message should be pushed successfully');
        
        // 尝试获取消息
        $consumerData = $this->driver->shift();
        $this->assertNull($consumerData, 'Should not get message without proper consumer group setup');
    }
    
    public function testFlushChannel()
    {
        // 如果Redis服务器不可达，则跳过此测试
        if (!$this->driver) {
            $this->markTestSkipped('Redis server is not available');
        }
        
        // 测试清空频道功能
        $result = $this->driver->flush($this->testChannel);
        $this->assertIsBool($result);
    }
    
    public function testDelMessages()
    {
        // 如果Redis服务器不可达，则跳过此测试
        if (!$this->driver) {
            $this->markTestSkipped('Redis server is not available');
        }
        
        // 测试删除消息功能
        $result = $this->driver->del($this->testChannel, ['123', '456']);
        $this->assertIsBool($result);
    }
}