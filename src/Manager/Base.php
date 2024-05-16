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
use Playcat\Queue\Exceptions\ConnectFailExceptions;
use Playcat\Queue\Producer\Producer;
use Playcat\Queue\Protocols\ConsumerDataInterface;
use Playcat\Queue\Protocols\ProducerDataInterface;

class Base implements ManegerInterface
{
    protected static $instance;
    protected $manager_config;
    protected $producer;
    protected $tc;
    protected $restry_maxtimes = 5;
    protected $retry_times = 0;

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
        return $payload->getDelayTime() > 0 ?
            $this->send2ts('push', $payload) :
            $this->getProducer()->push($payload);
    }

    public function consumerFinished(): bool
    {
        return $this->getProducer()->consumerFinished();
    }

    public function del(ProducerDataInterface $payload): bool
    {
        return $this->send2ts('del', $payload);
    }

    /**
     * @param string $command
     * @param ProducerDataInterface $payload
     * @return string
     * @throws ConnectFailExceptions
     */
    private function send2ts(string $command, ProducerDataInterface $payload): string
    {
        if ($this->retry_times >= $this->restry_maxtimes) {
            throw new ConnectFailExceptions('Connect to playcat time server failed.', 100);
        }
        try {
            $result = $this->getTimeClient()->$command($payload);
        } catch (ConnectFailExceptions $exception) {
            //OK,when network have issure or other,will auto reconnect to TS.
            sleep(2);
            $this->retry_times++;
            $result = $this->send2ts($command, $payload);
        }
        $this->retry_times = 0;
        return $result;
    }
}
