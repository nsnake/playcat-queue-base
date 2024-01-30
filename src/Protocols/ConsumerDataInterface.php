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

interface ConsumerDataInterface
{
    /**
     * @param string $id
     * @return void
     */
    public function setID(string $id): void;


    /**
     * @return string
     */
    public function getChannel(): string;

    /**
     * @return int
     */
    public function getRetryCount(): int;

    /**
     * @return array
     */
    public function getQueueData(): ?array;

    /**
     * @param string|array $serialize_data
     * @return array|null
     */
    public function unSerializeData($serialize_data): array;
}


