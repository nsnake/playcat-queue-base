<?php
/**
 *
 *
 * @license MIT License (MIT)
 *
 * For full copyright and license information, please see the LICENCE files.
 *
 * @author CGI.NET <318274085>
 */

namespace Playcat\Queue\Driver;

use Playcat\Queue\Model\Payload;
use Playcat\Queue\Protocols\ConsumerDataInterface;
use Playcat\Queue\Protocols\ProducerDataInterface;

interface DriverInterface
{
    public function setIconicId(int $iconic_id = 0): void;

    public function subscribe(array $channels): bool;

    public function shift(): ?ConsumerDataInterface;

    public function push(ProducerDataInterface $payload): ?string;

    public function consumerFinished(): bool;

    public function flush(string $channel): int|bool;

    public function del(string $channel, array $ids): int|bool;
}

