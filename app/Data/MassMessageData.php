<?php

namespace App\Data;

use App\Enums\PriorityEnum;
use Spatie\LaravelData\Data;

class MassMessageData extends Data
{
    public function __construct(
        public string $channel,
        public string $priority = PriorityEnum::LOW->value,
        public string $message,
        public array $userIds,
    ) {}
}
