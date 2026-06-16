<?php

namespace App\Enums;

enum MassMessageStatusEnum: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';

    public function description(): string
    {
        return match ($this) {
            self::PENDING => 'Ожидает обработки',
            self::PROCESSING => 'Обработка в процессе',
            self::COMPLETED => 'Завершена успешно',
            self::FAILED => 'Не удалась',
        };
    }
}
