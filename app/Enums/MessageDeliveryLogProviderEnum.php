<?php

namespace App\Enums;

enum MessageDeliveryLogProviderEnum: string
{
    case SMS_PROVIDER = 'sms_provider';
    case EMAIL_PROVIDER = 'email_provider';

    public function description(): string
    {
        return match ($this) {
            self::SMS_PROVIDER => 'SMS Provider',
            self::EMAIL_PROVIDER => 'Email Provider',
        };
    }
}
