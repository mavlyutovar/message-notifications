<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class NotificationProviderResponseData extends Data
{
    public function __construct(
        public bool $success,
        public ?string $messageId = null,
        public ?int $httpStatus = null,
        public ?string $error = null,
    ) {}
}
