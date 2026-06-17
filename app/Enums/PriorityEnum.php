<?php

namespace App\Enums;

enum PriorityEnum: string
{
    case LOW = 'low';
    case HIGH = 'high';

    public function description(): string
    {
        return match ($this) {
            self::LOW => 'Низкий приоритет',
            self::HIGH => 'Высокий приоритет ',
        };
    }

    public function weight(): int
    {
        return match ($this) {
            self::LOW => 1,
            self::HIGH => 2,
        };
    }
}
