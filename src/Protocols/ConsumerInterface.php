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
interface ConsumerInterface
{
    public function consume(ConsumerData $data);
}
