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
    protected static $client = null;
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
        if (self::$client === null) {
            $context = stream_context_create();
            if (!preg_match('/^unix:(.*)$/i', $this->config['timerserver'], $matches)) {
                $this->config['timerserver'] = 'tcp://' . $this->config['timerserver'];
            }
            $socket = @stream_socket_client(
                $this->config['timerserver'],
                $error,
                $errorMessage,
                3,
                STREAM_CLIENT_CONNECT,
                $context
            );

            if ($socket === false) {
                throw new ConnectFailExceptions('Connect to playcat time server failed. ' . $errorMessage);
            }
            self::$client = $socket;
        }
        return self::$client;
    }

    /**
     * @return false|string
     * @throws ConnectFailExceptions
     */
    protected function socketRead()
    {
        $result = fread($this->getClient(), 2048);
        if ($result == false) {
            fclose(self::$client);
            self::$client = null;
        }
        return $result;
    }

    /**
     * @param string $protocols
     * @return false|int
     * @throws ConnectFailExceptions
     */
    protected function socketWrite(string $protocols)
    {
        $result = fwrite($this->getClient(), $protocols . "\n");
        if ($result == false) {
            fclose(self::$client);
            self::$client = null;
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
