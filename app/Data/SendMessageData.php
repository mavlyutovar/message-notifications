<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class SendMessageData extends Data
{
    public function __construct(
        public string $channel,
        public string $message,
        public int $recipientId,
    ) {}
}
