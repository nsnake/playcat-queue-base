<?php
/**
 *
 *
 * @license MIT License (MIT)
 *
 * For full copyright and license information, please see the LICENCE files.
 *
 * @author CGI.NET
 */

namespace Playcat\Queue\Install;

use think\db\Query;
use think\DbManager;
use think\Exception;

class InitDB
{
    private $db;

    /**
     * @param array $config
     * @return void
     */
    public function setDriver(array $config): void
    {
        $db = new DbManager();
        $db->setConfig([
            'default' => $config['type'],
            'connections' => [
                $config['type'] => $config
            ]
        ]);
        $this->db = new Query($db->connect());
    }

    /**
     * For mysql sql.
     * @return void
     */
    public function initMysql()
    {
        $sql = 'DROP TABLE IF EXISTS `jobs`;CREATE TABLE `jobs`  (`jid` int(0) UNSIGNED NOT NULL AUTO_INCREMENT,`timerid` int(0) NOT NULL DEFAULT -1,`iconicid` int(0) NOT NULL DEFAULT -1,`data` blob NULL,`expiration` int(0) UNSIGNED NOT NULL DEFAULT 0,PRIMARY KEY (`jid`) USING BTREE) ENGINE = InnoDB ROW_FORMAT = Dynamic';
        $this->queue($sql);
    }

    /**
     * For sqlite file
     * @return void
     */
    public function initSqlite()
    {
        $sql = 'DROP TABLE IF EXISTS "jobs";CREATE TABLE "jobs" ("jid" integer NOT NULL PRIMARY KEY AUTOINCREMENT,"timerid" integer NOT NULL DEFAULT 0,"iconicid" integer NOT NULL DEFAULT 0,"data" blob,"expiration" integer NOT NULL DEFAULT 0)';
        $this->queue($sql);
    }

    /**
     * @param string $sql
     * @return bool
     */
    private function queue(string $sql): bool
    {
        return $this->db->batchQuery($sql);
    }
}