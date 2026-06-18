<?php

namespace App\Enums;

enum MessageRecipientStatusEnum: string
{
    case QUEUED = 'queued';
    case PROCESSING = 'processing';
    case DELIVERED = 'delivered';
    case FAILED = 'failed';
    case SYSTEM_FAILED = 'system_failed';

    public function description(): string
    {
        return match ($this) {
            self::QUEUED => 'Ожидает очереди',
            self::PROCESSING => 'Обработка в процессе',
            self::DELIVERED => 'Доставлено',
            self::FAILED => 'Ошибка',
            self::SYSTEM_FAILED => 'Системная Ошибка',
        };
    }
}
