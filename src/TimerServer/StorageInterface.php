<?php

namespace Playcat\Queue\TimerServer;


interface StorageInterface
{
    public function setDriver(array $config): void;

    public function addData(int $iconic_id, string $data): int;

    public function upData(int $j_id, int $timer_id, int $expiration): bool;

    public function getDataById(int $j_id): array;

    public function delData(int $j_id): bool;


}


