<?php

namespace App\Enums;

enum MassMessageChannelEnum: string
{
    case SMS = 'sms';
    case EMAIL = 'email';

    public function description(): string
    {
        return match ($this) {
            self::SMS => 'SMS',
            self::EMAIL => 'Email',
        };
    }
}
