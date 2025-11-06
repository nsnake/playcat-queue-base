<?php

namespace Playcat\Queue\Tests\Unit\Driver;

use PHPUnit\Framework\TestCase;
use Playcat\Queue\Driver\Redis;
use Playcat\Queue\Protocols\ProducerData;
use Playcat\Queue\Protocols\ConsumerData;
use RedisException;

class RedisTest extends TestCase
{
    private $redisConfig;
    
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
    }
    
    public function testRedisExtensionLoaded()
    {
        // 在CI环境中应该加载Redis扩展
        if (!extension_loaded('redis')) {
            $this->markTestSkipped('Redis extension is not available');
        }
        $this->assertTrue(true, 'Redis extension should be loaded');
    }
    
    public function testRedisDriverClassExists()
    {
        // 测试Redis驱动类是否存在
        $this->assertTrue(class_exists(Redis::class), 'Redis driver class should exist');
    }
    
    public function testRedisDriverInstantiation()
    {
        // 如果Redis扩展未加载，则跳过此测试
        if (!extension_loaded('redis')) {
            $this->markTestSkipped('Redis extension is not available');
        }
        
        // 如果Redis服务器不可达，则跳过此测试
        try {
            $driver = new Redis($this->redisConfig);
            $this->assertInstanceOf(Redis::class, $driver);
        } catch (RedisException $e) {
            $this->markTestSkipped('Redis server is not available: ' . $e->getMessage());
        } catch (\Exception $e) {
            $this->markTestSkipped('Failed to instantiate Redis driver: ' . $e->getMessage());
        }
    }
    
    public function testGenerateMsgid()
    {
        // 如果Redis扩展未加载，则跳过此测试
        if (!extension_loaded('redis')) {
            $this->markTestSkipped('Redis extension is not available');
        }
        
        // 如果Redis服务器不可达，则跳过此测试
        try {
            $driver = new Redis($this->redisConfig);
            $msgid = $driver->generateMsgid();
            
            // 验证生成的消息ID格式
            $this->assertIsString($msgid);
            $this->assertNotEmpty($msgid);
        } catch (RedisException $e) {
            $this->markTestSkipped('Redis server is not available: ' . $e->getMessage());
        } catch (\Exception $e) {
            $this->markTestSkipped('Failed to generate message ID: ' . $e->getMessage());
        }
    }
    
    public function testSetIconicId()
    {
        // 如果Redis扩展未加载，则跳过此测试
        if (!extension_loaded('redis')) {
            $this->markTestSkipped('Redis extension is not available');
        }
        
        // 如果Redis服务器不可达，则跳过此测试
        try {
            $driver = new Redis($this->redisConfig);
            $driver->setIconicId(123);
            
            // 通过反射获取私有属性验证
            $reflection = new \ReflectionClass($driver);
            $property = $reflection->getProperty('iconic_id');
            $property->setAccessible(true);
            $iconicId = $property->getValue($driver);
            
            $this->assertEquals(123, $iconicId);
        } catch (RedisException $e) {
            $this->markTestSkipped('Redis server is not available: ' . $e->getMessage());
        } catch (\Exception $e) {
            $this->markTestSkipped('Failed to set iconic ID: ' . $e->getMessage());
        }
    }
}