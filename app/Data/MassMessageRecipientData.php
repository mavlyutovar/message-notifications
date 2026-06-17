<?php

namespace App\Data;

use App\Enums\MessageRecipientStatusEnum;
use Spatie\LaravelData\Data;

class MassMessageRecipientData extends Data
{
    public function __construct(
        public int $mass_message_id,
        public int $user_id,
        public string $status = MessageRecipientStatusEnum::QUEUED->value,
        public ?string $last_error = null,
        public int $attempts = 0,
    ) {}
}
