<?php


namespace Playcat\Queue\Util;

use Exception;
use Psr\Container\ContainerInterface;


class Container implements ContainerInterface
{
    protected static $instance;

    protected $instances = [];

    public static function instance(): Container
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @throws Exception
     */
    public function get(string $name)
    {
        if (!isset($this->instances[$name])) {
            if (!class_exists($name)) {
                throw new Exception("Class '$name' not found");
            }
            $this->instances[$name] = new $name();
        }
        return $this->instances[$name];
    }

    /**
     * Has.
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return array_key_exists($name, $this->instances);
    }
}
