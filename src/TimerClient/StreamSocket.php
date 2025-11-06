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

use Playcat\Queue\Exceptions\DisconnectedExceptions;
use Playcat\Queue\Exceptions\ConnectFailExceptions;
use Playcat\Queue\Protocols\ProducerData;
use Playcat\Queue\Log\Log;

class StreamSocket implements TimerClientInterface
{
    protected static $client = null;
    protected array $config;
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return false|resource
     * @throws DisconnectedExceptions
     */
    protected function getClient()
    {
        if (self::$client === null) {
            Log::info('Connect To Timer Server!');
            if (
                !preg_match('/^unix:(.*)$/i', $this->config['timerserver'], $matches) &&
                !preg_match('/^tcp:(.*)$/i', $this->config['timerserver'], $matches)
            ) {
                $this->config['timerserver'] = 'tcp://' . $this->config['timerserver'];
            }
            $context = stream_context_create();
            $socket = @stream_socket_client($this->config['timerserver'], $error, $errorMessage, 3, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $context);
            if ($socket === false) {
                $message = 'Connect to playcat time server failed. ' . $errorMessage;
                Log::warning($message);
                throw new DisconnectedExceptions($message, 100);
            }

            self::$client = $socket;
        }
        return self::$client;
    }

    /**
     * @return string
     * @throws DisconnectedExceptions
     */
    protected function socketRead()
    {
        $result = fread($this->getClient(), 2048);
        if (!$result) {
            $message = 'Get data error!';
            Log::critical($message);
            throw new DisconnectedExceptions($message, 100);
        }
        return $result;
    }

    /**
     * @param string $protocols
     * @return int
     * @throws DisconnectedExceptions
     */
    protected function socketWrite(string $protocols)
    {
        $result = fwrite($this->getClient(), $protocols . "\n");
        if (!$result) {
            $message = 'Send data error!';
            Log::critical($message);
            throw new DisconnectedExceptions($message, 100);
        }
        return $result;
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
     * @return array
     */
    protected function unserializeProtocols(string $protocols): array
    {
        return json_decode($protocols, true) ?? [];
    }

    /**
     * @param string $command
     * @param ProducerData $payload
     * @return array
     * @throws ConnectFailExceptions
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
        } catch (DisconnectedExceptions $e) {
        //ok,let's reconnecten next time.
            $this->disconnect();
            $message = 'Disconnect Timerserver!';
            Log::critical($message);
            throw new ConnectFailExceptions($message, 100);
        }
        return $result;
    }

    /**
     * @param ProducerData $payload
     * @return string
     * @throws ConnectFailExceptions
     */
    public function push(ProducerData $payload): string
    {
        $result = $this->sendCommand(TimerClientProtocols::CMD_PUSH, $payload);
        return ($result && $result['code'] == 200) ? $result['data'] : '';
    }

    /**
     * @param ProducerData $payload
     * @return bool
     * @throws ConnectFailExceptions
     */
    public function del(ProducerData $payload): bool
    {
        $result = $this->sendCommand(TimerClientProtocols::CMD_DEL, $payload);
        return ($result && $result['code'] == 200) ? true : false;
    }


    /**
     * @return void
     */
    public function disconnect(): void
    {
        if (is_resource(self::$client)) {
            stream_socket_shutdown(self::$client, STREAM_SHUT_RDWR);
            self::$client = null;
        }
    }
}
