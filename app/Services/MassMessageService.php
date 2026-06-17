<?php

namespace App\Services;

use App\Data\MassMessageData;
use App\Models\MassMessage;
use App\Repositories\MassMessageRepository;

class MassMessageService
{
    public function __construct(
        private MassMessageRepository $repository,
    ) {}

    public function create(MassMessageData $data): MassMessage
    {
        return $this->repository->create([
            'channel' => $data->channel,
            'priority' => $data->priority,
            'message' => $data->message,
            'status' => \App\Enums\MassMessageStatusEnum::PENDING->value,
        ]);
    }

    public function find(int $id): ?MassMessage
    {
        return $this->repository->find($id);
    }

    public function updateStatus(int $massMessageId, string $status): void
    {
        $this->repository->updateStatus($massMessageId, $status);
    }

    public function getCollection(string $channel, int $limit): array
    {
        $collection = $this->repository->getFilteredCollection($channel, $limit);

        return [
            'items' => $collection->map(function (MassMessage $item): array {
                return [
                    'id' => $item->id,
                    'channel' => $item->channel,
                    'priority' => $item->priority,
                    'message_preview' => substr($item->message, 0, 100) . (strlen($item->message) > 100 ? '...' : ''),
                    'user_ids_count' => count($item->recipients), // Eager loaded через with() в репозитории
                    'status' => $item->status,
                    'created_at' => $item->created_at?->toDateTimeString() ?? '',
                ];
            })->toArray(),
            'total' => $this->repository->getFilteredTotalCount($channel),
        ];
    }
}
