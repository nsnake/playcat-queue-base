<?php

namespace Playcat\Queue\TimerServer;


interface StorageInterface
{
    public function setDriver(array $config): void;

    public function addData(int $iconic_id, int $expiration, object $data): int;

    public function upData(int $jid, int $timer_id): bool;

    public function getDataById(int $jid): array;

    public function delData(int $jid): bool;


}


