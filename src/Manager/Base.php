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

namespace Playcat\Queue\Manager;

use Playcat\Queue\Driver\DriverInterface;
use Playcat\Queue\Producer\Producer;

class Base implements ManegerInterface
{
    protected static $instance;
    protected $manager_config;
    protected $producer;
    protected $tc;

    final public static function getInstance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct()
    {
    }

    protected function getTimeClient()
    {
    }

    protected function getProducer(): DriverInterface
    {
        if (!$this->producer || !$this->producer instanceof DriverInterface) {
            $this->producer = (new Producer())->DriverByAuto($this->manager_config);
        }
        return $this->producer;
    }

    public function setIconicId(int $iconic_id = 0): void
    {
        $this->getProducer()->setIconicId($iconic_id);
    }

    public function subscribe(array $channels): bool
    {
        return $this->getProducer()->subscribe($channels);
    }

    public function shift(): ?ConsumerDataInterface
    {
        return $this->getProducer()->shift();
    }

    public function push(ProducerDataInterface $payload): ?string
    {
        return $payload->getDelayTime() > 0
            ? $this->getTimeClient()->push($payload) : $this->getProducer()->push($payload);
    }

    public function consumerFinished(): bool
    {
        return $this->getProducer()->consumerFinished();
    }

    public function del(ProducerDataInterface $payload): bool
    {
        return $this->getTimeClient()->del($payload);
    }
}