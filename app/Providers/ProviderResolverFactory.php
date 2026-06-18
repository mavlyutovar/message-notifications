<?php

namespace App\Providers;

use InvalidArgumentException;

class ProviderResolverFactory
{
    public function resolve(string $type): ProviderInterface
    {
        return match ($type) {
            'email' => app(EmailProvider::class),
            'sms'   => app(SmsProvider::class),
            default => throw new InvalidArgumentException("Unknown provider: $type"),
        };
    }
}
