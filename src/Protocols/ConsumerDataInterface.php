<?php

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
    public function unSerializeData(string|array $serialize_data): array|null;
}


