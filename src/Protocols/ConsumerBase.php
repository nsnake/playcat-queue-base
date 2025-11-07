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
    public const  MAX_ATTEMPTS = null;
    public const  RETRY_SECONDS = null;

    public function onInit()
    {
    }

    public function consume(ConsumerData $data)
    {
    }
}
