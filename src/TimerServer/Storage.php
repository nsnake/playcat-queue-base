<?php

namespace Playcat\Queue\TimerServer;

use Playcat\Queue\Protocols\ProducerData;
use think\db\Query;
use think\DbManager;

class Storage implements StorageInterface
{
    private $db;

    /**
     * @param array $config
     * @return void
     */
    public function setDriver(array $config): void
    {
        $db = new DbManager();
        $db->setConfig($config);
        $this->db = new Query($db->connect());
    }

    /**
     * @return Query
     */
    private function getTable(): Query
    {
        return $this->db->removeOption()->table('jobs');
    }

    /**
     * @param int $iconic_id
     * @param int $expiration
     * @param object $data
     * @return int
     */
    public function addData(int $iconic_id, int $expiration, object $data): int
    {
        return $this->getTable()->insertGetId([
            'iconicid' => $iconic_id,
            'data' => serialize($data),
            'expiration' => time() + $expiration
        ]);
    }

    /**
     * @param int $jid
     * @param int $timer_id
     * @return bool
     */
    public function upData(int $jid, int $timer_id): bool
    {
        return (bool)$this->getTable()->save([
            'jid' => $jid,
            'timerid' => $timer_id,
        ]);
        return $data;
    }

    /**
     * @param int $jid
     * @return array
     */
    public function getDataById(int $jid): array
    {
        $data = $this->getTable()
            ->where('jid', $jid)
            ->findOrEmpty();
        if ($data) {
            $data['data'] = $this->unserializeData($data['data']);
        }
        return $data;
    }

    /**
     * @param int $jid
     * @return int
     * @throws \think\db\exception\DbException
     */
    public function delData(int $jid): int
    {
        return $this->getTable()->delete($jid);
    }

    /**
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getHistoryJobs(): array
    {
        return $this->getTable()
            ->select()
            ->map(function ($item) {
                $item['data'] = $this->unserializeData($item['data']);
                return $item;
            })->toArray();
    }

    /**
     * @param string $data
     * @return ProducerData
     */
    private function unserializeData(string $serializeData): ProducerData
    {
        return unserialize($serializeData);
    }

}


