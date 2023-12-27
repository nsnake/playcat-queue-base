<?php

namespace Playcat\Queue\TimerServer;


interface StorageInterface
{
    public function setDriver(array $config): void;

    public function addData(int $iconic_id, string $data): int;

    public function upData(int $ts_id, int $timer_id): bool;

    public function getDataById(int $ts_id): array;

    public function delData(int $ts_id): bool;


}


