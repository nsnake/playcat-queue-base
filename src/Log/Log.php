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

/**
 * @method static debug(string $message, array $context = []);
 * 调试信息
 * @method static info(string $message, array $context = []);
 * 信息
 * @method static notice(string $message, array $context = []);
 * 通知
 * @method static warning(string $message, array $context = []);
 * 警告
 * @method static error(string $message, array $context = []);
 * 一般错误
 * @method static critical(string $message, array $context = []);
 * 危险错误
 * @method static alert(string $message, array $context = []);
 * 警戒错误
 * @method static emergency(string $message, array $context = []);
 * 紧急错误
 *
 * @see LogDriverInterface
 */
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
            $args[0] = sprintf('[PlaycatQueue] %s', $args[0]);
            self::$log_handle::$method_name(...$args);
        }
    }
}