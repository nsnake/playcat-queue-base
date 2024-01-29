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

interface TimerClientInterface
{

    public function push(ProducerData $payload): string;

    public function del(ProducerData $payload): bool;
}

