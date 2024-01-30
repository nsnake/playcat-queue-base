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

interface ProducerDataInterface
{

    /**
     * @param string $id
     * @return void
     */
    public function setID(string $id): void;

    /**
     * @return string
     */
    public function getID(): string;

    /**
     * @param string $channel
     * @return void
     */
    public function setChannel(string $channel = 'default'): void;

    /**
     * @return string
     */
    public function getChannel(): string;


    /**
     * @param int $count
     * @return void
     */
    public function setRetryCount(int $count = 0): void;

    /**
     * @return int
     */
    public function getRetryCount(): int;

    /**
     * @param  $data
     * @return void
     */
    public function setQueueData($data): void;

    /**
     * @return array
     */
    public function getQueueData(): ?array;

    /**
     * @param int $delay_time
     * @return mixed
     */
    public function setDelayTime(int $delay_time = 0);

    /**
     * @return int
     */
    public function getDelayTime(): int;

    /**
     * For redis, xadd only accept hash not string.
     * @param bool $is_redis
     * @return array|string
     */
    public function serializeData(bool $is_redis): array;


}


