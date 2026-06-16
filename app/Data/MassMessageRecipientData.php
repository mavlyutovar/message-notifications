<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class MassMessageRecipientData extends Data
{
    public function __construct(
        public int $mass_message_id,
        public int $user_id,
        public string $status = 'queued',
        public int $attempts = 0,
        public ?string $last_error = null,
    ) {}
}
