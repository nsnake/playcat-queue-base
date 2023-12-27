<?php

namespace Playcat\Queue\TimerServer;

use think\facade\Db;

class Storage implements StorageInterface
{

    /**
     * @param array $config
     * @return void
     */
    public function setDriver(array $config): void
    {
        Db::setConfig($config);

    }

    /**
     * @return Db
     */
    private function getTable(): Db
    {
        return Db::table('timerserver');
    }

    /**
     * @param int $iconic_id
     * @param int $timer_id
     * @param string $data
     * @return bool
     */
    public function addData(int $iconic_id, int $timer_id, string $data): int
    {
        return $this->getTable()->insertGetId([
            'iconic_id' => $iconic_id,
            'timer_id' => $timer_id,
            'data' => $data
        ]);
    }

    /**
     * @param int $ts_id
     * @param int $iconic_id
     * @param int $timer_id
     * @return bool
     */
    public function upData(int $ts_id, int $iconic_id, int $timer_id): bool
    {
        return (bool)$this->getTable()->save([
            'ts_id' => $ts_id,
            'iconic_id' => $iconic_id,
            'timer_id' => $timer_id,
            'data' => $data
        ]);
    }

    /**
     * @param int $ts_id
     * @return bool
     */
    public function delData(int $ts_id): bool
    {
        return (bool)$this->getTable()->delete($ts_id);
    }


}


