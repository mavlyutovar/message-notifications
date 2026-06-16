<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class MassMessageData extends Data
{
    public function __construct(
        public string $channel,
        public string $message,
        public array $userIds,
    ) {}
}
