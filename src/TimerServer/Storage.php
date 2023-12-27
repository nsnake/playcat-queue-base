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
        return Db::table('jobs');
    }

    /**
     * @param int $iconic_id
     * @param string $data
     * @return int
     */
    public function addData(int $iconic_id, string $data): int
    {
        return $this->getTable()->insertGetId([
            'iconicid' => $iconic_id,
            'timerid' => $timer_id,
            'data' => $data
        ]);
    }

    /**
     * @param int $ts_id
     * @param int $timer_id
     * @return bool
     */
    public function upData(int $ts_id, int $timer_id): bool
    {
        return (bool)$this->getTable()->save([
            'tsid' => $ts_id,
            'timerid' => $timer_id,
        ]);
    }

    /**
     * @param int $ts_id
     * @return array
     */
    public function getDataById(int $ts_id): array
    {
        return $this->getTable()
            ->where('ts_id', $ts_id)
            ->findOrEmpty();
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


