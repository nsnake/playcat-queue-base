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

use think\DbManager;

class InitDB
{
    private $db;
    private $config;

    /**
     * @param array $config
     * @return void
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * For mysql sql.
     * @return bool
     */
    public function initMysql(): bool
    {
        $db = new DbManager();
        $db->setConfig([
            'default' => $this->config['type'],
            'connections' => [
                $this->config['type'] => $this->config
            ]
        ]);
        $this->db = $db->connect();
        $sql = 'CREATE TABLE `jobs`  (`jid` int(0) UNSIGNED NOT NULL AUTO_INCREMENT,`timerid` int(0) NOT NULL DEFAULT -1,`iconicid` int(0) NOT NULL DEFAULT -1,`data` blob NULL,`expiration` int(0) UNSIGNED NOT NULL DEFAULT 0,PRIMARY KEY (`jid`) USING BTREE) ENGINE = InnoDB;';
        $is_success = true;
        try {
            $this->db->execute($sql);
        } catch (\Exception $e) {
            $is_success = false;
        }
        return $is_success;
    }

    /**
     * @return bool
     */
    public function initSqlite(): bool
    {
        $sql = 'CREATE TABLE "jobs" ("jid" integer NOT NULL PRIMARY KEY AUTOINCREMENT,"timerid" integer NOT NULL DEFAULT 0,"iconicid" integer NOT NULL DEFAULT 0,"data" blob,"expiration" integer NOT NULL DEFAULT 0);';
        $pdo = new \PDO('sqlite:' . $this->config['database']);
        $result = $pdo->exec($sql);
        return $result === 0 ? true : false;
    }
}
