<?php
/**
 *
 *
 * @license MIT License (MIT)
 *
 * For full copyright and license information, please see the LICENCE files.
 *
 * @author CGI.NET
 */

namespace Playcat\Queue\TimerClient;

use Playcat\Queue\Exceptions\ConnectFailExceptions;
use Playcat\Queue\Protocols\ProducerData;

class StreamSocket implements TimerClientInterface
{
    protected static $client;
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return false|resource
     * @throws ConnectFailExceptions
     */
    protected function getClient()
    {
        if (!self::$client) {
            $context = stream_context_create();
            if (str_starts_with($host, 'unix://')) {
                $socket = @stream_socket_client($host, $error, $errorMessage, 3, STREAM_CLIENT_CONNECT, $context);
            } else {
                $socket = @stream_socket_client(
                    "tcp://$host:$port",
                    $error,
                    $errorMessage,
                    3,
                    STREAM_CLIENT_CONNECT,
                    $context
                );
            }
            if ($socket === false) {
                throw new ConnectFailExceptions('Connect to playcat time server failed. ' . $errstr);
            }
            stream_set_timeout($socket, 3);
        }
        return self::$client;
    }

    /**
     * @param string $command
     * @param ProducerData $payload
     * @return array
     */
    protected function sendCommand(string $command, ProducerData $payload): array
    {
        $result = [];
        try {
            $protocols = new TimerClientProtocols();
            $protocols->setCMD($command);
            $protocols->setPayload($payload);
            $this->getClient()->send(serialize($protocols) . "\r\n");
            $result = $this->client()->recv();
            $result = json_decode($result, true) ?? [];
        } catch (ConnectFailExceptions $e) {
        }
        return $result;
    }

    /**
     * join a delay message
     * @param ProducerData $payload
     * @return string
     */
    public function push(ProducerData $payload): string
    {
        $result = $this->sendCommand(TimerClientProtocols::CMD_PUSH, $payload);
        return ($result && $result['code'] == 200) ? $result['data'] : '';
    }

    /**
     * delete a delay message
     * @param ProducerData $payload
     * @return bool
     */
    public function del(ProducerData $payload): bool
    {
        $result = $this->sendCommand(TimerClientProtocols::CMD_DEL, $payload);
        return ($result && $result['code'] == 200) ? true : false;
    }


}