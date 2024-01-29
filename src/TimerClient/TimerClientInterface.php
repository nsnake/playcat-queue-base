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

use Playcat\Queue\Protocols\ProducerData;
use Playcat\Queue\TimerClient\TimerClientProtocols;

interface TimerClientInterface
{

    function sendCommand(string $command, ProducerData $payload): array;

    function push(ProducerData $payload): string;

    function del(ProducerData $payload): bool;
}

