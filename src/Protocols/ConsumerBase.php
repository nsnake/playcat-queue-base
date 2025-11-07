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

namespace Playcat\Queue\Protocols;

/**
 * Interface Consumer
 */
class ConsumerBase implements ConsumerInterface
{
    public const max_attempts = null;
    public const retry_seconds = null;

    public function onInit()
    {
    }

    public function consume(ConsumerData $data)
    {
    }
}
