<?php

namespace App\Services;

use App\Data\NotificationProviderResponseData;
use App\Models\User;
use App\Providers\ProviderResolverFactory;

class NotificationService
{
    public function __construct(
        private ProviderResolverFactory $resolver
    ) {}

    public function send(string $type, User $user, string $message): NotificationProviderResponseData
    {
        $provider = $this->resolver->resolve($type);

        return $provider->send($user, $message);
    }
}
