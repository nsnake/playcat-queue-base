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

use Playcat\Queue\Protocols\ProducerDataInterface;

class TimerClientProtocols
{
    const CMD_PUSH = 'push';
    const CMD_DEL = 'del';
    const CMD_PING = 'ping';

    protected $ver = '1.1';
    protected $cmd;

    protected $payload;

    public function setCMD(string $cmd): void
    {
        $this->cmd = $cmd;
    }

    public function getCMD(): string
    {
        return $this->cmd;
    }

    public function setPayload(ProducerDataInterface $producerdata): void
    {
        $this->payload = $producerdata;
    }

    public function getPayload(): ProducerDataInterface
    {
        return $this->payload;
    }

    public function getVer(): string
    {
        return $this->ver;
    }
}


