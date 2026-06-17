<?php

namespace App\Actions;

use App\Models\MassMessage as MassMessageModel;
use App\Services\MassMessageService;
use Illuminate\Support\Facades\Log;

final readonly class GetMassMessageStatusAction
{
    public function __construct(
        private MassMessageService $service,
    ) {}

    public function execute(int $id): ?array
    {
        $massMessage = $this->service->find($id);

        if (!$massMessage) {
            return null;
        }

        return [
            'id' => $massMessage->id,
            'channel' => $massMessage->channel,
            'priority' => $massMessage->priority,
            'message' => $massMessage->message,
            'user_ids_count' => count($massMessage->recipients),
            'status' => $massMessage->status,
            'created_at' => $massMessage->created_at?->toDateTimeString() ?? '',
        ];
    }
}
