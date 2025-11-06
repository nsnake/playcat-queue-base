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

use Playcat\Queue\Protocols\ConsumerDataInterface;
use Playcat\Queue\Protocols\ProducerDataInterface;

interface ManegerInterface
{
    public function setIconicId(int $iconic_id = 0): void;
    public function subscribe(array $channels): bool;
    public function shift(): ?ConsumerDataInterface;
    public function push(ProducerDataInterface $payload): ?string;
    public function consumerFinished(): bool;
    public function del(ProducerDataInterface $payload): bool;
}
