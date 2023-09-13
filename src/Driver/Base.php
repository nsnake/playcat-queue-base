<?php

namespace Playcat\Queue\Driver;
class Base
{
    protected $iconic_id = 0;

    /**
     * @param int $iconic_id
     * @return void
     */
    public function setIconicId(int $iconic_id = 0): void
    {
        $this->iconic_id = $iconic_id;
    }

    /**
     * Generate a unique message ID
     * @return string
     */
    public function generateMsgid(): string
    {
        return sprintf(
            '%s-%04x-%04x-%04x-%04x%04x', $this->iconic_id, mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
