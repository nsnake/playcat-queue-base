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
     * @param int $j_id
     * @param int $timer_id
     * @return bool
     */
    public function upData(int $j_id, int $timer_id,int $expiration): bool
    {
        return (bool)$this->getTable()->save([
            'jid' => $j_id,
            'timerid' => $timer_id,
            'expiration' => $expiration
        ]);
    }

    /**
     * @param int $j_id
     * @return array
     */
    public function getDataById(int $j_id): array
    {
        return $this->getTable()
            ->where('j_id', $j_id)
            ->findOrEmpty();
    }

    /**
     * @param int $j_id
     * @return bool
     */
    public function delData(int $j_id): bool
    {
        return (bool)$this->getTable()->delete($j_id);
    }


}


