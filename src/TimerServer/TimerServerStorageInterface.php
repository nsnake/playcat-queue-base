<?php

namespace Playcat\Queue\TimerServer;


interface StorageInterface
{
    public function setDriver(array $config): void;

    public function addData(int $iconic_id, int $timer_id, string $data): int;

    public function upData(int $id, int $iconic_id, int $timer_id): bool;

    public function delData(int $id): bool;


}


