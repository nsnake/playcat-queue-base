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

class ProducerData implements ProducerDataInterface
{
    protected $channel = 'default';
    protected $retry_count = 0;
    protected $queue_data = '[]';
    protected $delay_time = 0;
    protected $id = '';

    /**
     * @param string $id
     * @return void
     */
    public function setID(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getID(): string
    {
        return $this->id;
    }

    /**
     * @param string $channel
     * @return void
     */
    public function setChannel(string $channel = 'default'): void
    {
        $this->channel = $channel;
    }

    /**
     * @return string
     */
    public function getChannel(): string
    {
        return $this->channel;
    }


    /**
     * @param int $count
     * @return void
     */
    public function setRetryCount(int $count = 0): void
    {
        $this->retry_count = $count;
    }

    /**
     * @return int
     */
    public function getRetryCount(): int
    {
        return $this->retry_count;
    }

    /**
     * @param array $data
     * @return void
     */
    public function setQueueData($data): void
    {
        $this->queue_data = $data;
    }

    /**
     * @return array
     */
    public function getQueueData(): ?array
    {
        return $this->queue_data;
    }

    public function setDelayTime(int $delay_time = 0)
    {
        $this->delay_time = $delay_time;
    }

    public function getDelayTime(): int
    {
        return $this->delay_time;
    }

    public function serializeData(bool $is_redis = false)
    {
        $data = msgpack_pack([
            'id' => $this->id,
            'channel' => $this->channel,
            'creattime' => time(),
            'retrycount' => $this->retry_count,
            'queuedata' => $this->queue_data,
            'delaytime' => $this->delay_time
        ]) ?? [];
        return $is_redis === false ? $data : ["data" => $data];
    }


}
