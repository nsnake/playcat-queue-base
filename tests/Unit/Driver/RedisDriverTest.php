<?php

namespace Playcat\Queue\Tests\Unit\Driver;

use PHPUnit\Framework\TestCase;
use Playcat\Queue\Driver\Redis;
use Playcat\Queue\Exceptions\ConnectFailExceptions;

class RedisDriverTest extends TestCase
{
    private Redis $driver;

    protected function setUp(): void
    {
        $config = [
            'host' => '127.0.0.1',
            'port' => 6379,
            'password' => '',
            'db' => 0,
        ];

        $this->driver = new Redis($config);
    }

    public function testConnection()
    {
        $this->assertInstanceOf(Redis::class, $this->driver);
    }

    public function testConnectionFailure()
    {
        $this->expectException(ConnectFailExceptions::class);

        $config = [
            'host' => '127.0.0.1',
            'port' => 6380, // Wrong port
            'password' => '',
            'db' => 0,
        ];

        new Redis($config);
    }
}
