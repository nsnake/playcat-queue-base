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

use Swoole\Coroutine\Client;
use Playcat\Queue\Exceptions\ConnectFailExceptions;
use Playcat\Queue\Protocols\ProducerData;

class SwooleScoket extends StreamSocket
{
    /**
     * @return false|resource
     * @throws ConnectFailExceptions
     */
    final protected function getClient()
    {
        if (!self::$client) {
            $ts_host = 'localhost';
            $ts_port = 0;
            if (preg_match('/^unix:(.*)$/i', $this->config['timerserver'], $matches)) {
                $ts_host = $matches[1];
                self::$client = new Client(SWOOLE_SOCK_UNIX_STREAM);
            } else {
                preg_match('/^((\d+\.\d+\.\d+\.\d+)|\w+):(\d+)$/', $this->config['timerserver'], $matches);
                $ts_host = $matches[1];
                $ts_port = $matches[3];
                self::$client = new Client(SWOOLE_SOCK_TCP);
            }

            if (!self::$client->connect($ts_host, $ts_port, 1)) {
                throw new ConnectFailExceptions('Connect to playcat time server failed. ' . $errstr);
            }
        }
        return self::$client;
    }

    final protected function socketRead()
    {
        return $this->getClient()->recv();
    }

    final protected function socketWrite(string $protocols)
    {
        return $this->getClient()->send($protocols) . "\r\n";
    }

}
