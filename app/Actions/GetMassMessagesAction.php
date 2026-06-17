<?php

namespace App\Actions;

use App\Services\MassMessageService;

final readonly class GetMassMessagesAction
{
    public function __construct(
        private MassMessageService $service,
    ) {}

    public function execute(string $channel, int $limit = 50): array
    {
        $limit = min(max(1, $limit), 50);
        $massMessages = $this->service->getCollection($channel, $limit);

        return [
            'items' => $massMessages['items'] ?? [],
            'total' => $massMessages['total'] ?? 0,
        ];
    }
}
