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
            $socket = @stream_socket_client(
                'tcp://' . $this->config['timerserver'],
                $error,
                $errorMessage,
                3,
                STREAM_CLIENT_CONNECT,
                $context
            );

            if ($socket === false) {
                throw new ConnectFailExceptions('Connect to playcat time server failed. ' . $errstr);
            }
            stream_set_timeout($socket, 3);
        }
        return self::$client;
    }

    /**
     * @return false|string
     * @throws ConnectFailExceptions
     */
    protected function socketRead()
    {
        return fread($this->getClient(), 2048);
    }

    /**
     * @param string $protocols
     * @return false|int
     * @throws ConnectFailExceptions
     */
    protected function socketWrite(string $protocols)
    {
        return fwrite($this->getClient(), $protocols . "\n");
    }

    /**
     * @param TimerClientProtocols $protocols
     * @return string
     */
    protected function serializeProtocols(TimerClientProtocols $protocols): string
    {
        return serialize($protocols);
    }

    /**
     * @param string $protocols
     * @return array|false
     */
    protected function unserializeProtocols(string $protocols): array
    {
        return json_decode($protocols, true) ?? [];
    }

    /**
     * @param string $command
     * @param ProducerData $payload
     * @return array
     */
    public function sendCommand(string $command, ProducerData $payload): array
    {
        $result = [];
        try {
            $protocols = new TimerClientProtocols();
            $protocols->setCMD($command);
            $protocols->setPayload($payload);
            $this->socketWrite($this->serializeProtocols($protocols));
            $result = $this->unserializeProtocols($this->socketRead());
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
