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

namespace Playcat\Queue\Log;

class Log
{
    private static $log_handle;

    /**
     * @param $log_handle
     * @return void
     */
    public static function setLogHandle($log_handle)
    {
        self::$log_handle = $log_handle;
    }

    /**
     * @param string $level
     * @param array|string $message
     * @return void
     */
    public static function __callStatic(string $method_name, array $args): void
    {
        if (self::$log_handle) {
            self::$log_handle::$method_name(...$args);
        }
    }
}