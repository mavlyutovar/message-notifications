<?php

namespace App\Enums;

enum MessageRecipientStatusEnum: string
{
    case QUEUED = 'queued';
    case SENT = 'sent';
    case DELIVERED = 'delivered';
    case FAILED = 'failed';

    public function description(): string
    {
        return match ($this) {
            self::QUEUED => 'Очередь',
            self::SENT => 'Отправлено',
            self::DELIVERED => 'Доставлено',
            self::FAILED => 'Ошибка',
        };
    }
}
