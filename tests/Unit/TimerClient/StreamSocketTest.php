<?php

namespace Playcat\Queue\Tests\Unit\TimerClient;

use PHPUnit\Framework\TestCase;
use Playcat\Queue\TimerClient\StreamSocket;
use Mockery;

class StreamSocketTest extends TestCase
{
    private $config;

    protected function setUp(): void
    {
        $this->config = [
            'host' => '127.0.0.1',
            'port' => 9501,
            'timeout' => 5,
        ];
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function testConnect()
    {
        $client = new StreamSocket($this->config);
        $this->assertInstanceOf(StreamSocket::class, $client);
    }

    public function testSendMessage()
    {
        $client = new StreamSocket($this->config);
        $data = [
            'type' => 'timer',
            'data' => [
                'delay' => 60,
                'message' => 'test message'
            ]
        ];

        $result = $client->send($data);
        $this->assertTrue($result);
    }

    public function testReceiveMessage()
    {
        $client = new StreamSocket($this->config);
        $response = $client->receive();

        $this->assertIsArray($response);
        $this->assertArrayHasKey('type', $response);
        $this->assertArrayHasKey('data', $response);
    }

    public function testConnectionFailure()
    {
        $this->expectException(\Exception::class);

        $invalidConfig = [
            'host' => '127.0.0.1',
            'port' => 9999, // Invalid port
            'timeout' => 1,
        ];

        $client = new StreamSocket($invalidConfig);
        $client->connect();
    }
}
