<?php

namespace Playcat\Queue\Protocols;

use Playcat\Queue\Exceptions\ParamsError;

class ConsumerData implements ConsumerDataInterface
{
    protected $channel = 'default';
    protected $creat_time = 0;
    protected $retry_count = 0;
    protected $queue_data = '[]';
    protected $delay_time = 0;
    protected $id = '';

    /**
     * @param array $payload
     * @throws ParamsError
     */
    public function __construct($payload = '')
    {
        if (!empty($payload)) {
            $payload = $this->unSerializeData($payload);
            foreach (['creattime', 'channel', 'retrycount', 'queuedata', 'delaytime'] as $value) {
                if (!isset($payload[$value])) {
                    throw new ParamsError('Error payload data. ignore it!');
                }
            }
            $this->creat_time = $payload['creattime'];
            $this->channel = $payload['channel'];
            $this->retry_count = $payload['retrycount'];
            $this->queue_data = $payload['queuedata'];
            $this->delay_time = $payload['delaytime'];
        }
    }

    public function setID(string $id): void
    {
        $this->id = $id;
    }


    public function getChannel(): string
    {
        return $this->channel;
    }


    public function getRetryCount(): int
    {
        return $this->retry_count;
    }


    public function getQueueData(): ?array
    {
        return $this->queue_data;
    }

    public function unSerializeData($serialize_data): array
    {
        $serialize_data = !is_array($serialize_data)
            ? $serialize_data
            : $serialize_data['data'];
        return msgpack_unpack($serialize_data) ?? [];
    }

}


