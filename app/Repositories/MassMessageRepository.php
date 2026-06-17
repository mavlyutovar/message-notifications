<?php

namespace App\Repositories;

use App\Models\MassMessage;

class MassMessageRepository
{
    public function create(array $data): MassMessage
    {
        return MassMessage::query()->create($data);
    }

    public function find(int $id): ?MassMessage
    {
        return MassMessage::query()->find($id);
    }

    public function updateStatus(int $massMessageId, string $status): void
    {
        $model = MassMessage::query()->findOrFail($massMessageId);
        $model->update(['status' => $status]);
    }

    public function getFilteredCollection(string $channel, int $limit): \Illuminate\Database\Eloquent\Collection
    {
        $query = MassMessage::query()
            ->orderByDesc('created_at')
            ->with(['recipients']);

        if ($channel) {
            $query->where('channel', $channel);
        }

        return $query->take($limit)->get();
    }

    public function getMessagesByPriority(string $channel, array $priorities): \Illuminate\Database\Eloquent\Collection
    {
        $query = MassMessage::query()
            ->whereIn('priority', $priorities)
            ->orderByDesc('created_at')
            ->with(['recipients']);

        if ($channel) {
            $query->where('channel', $channel);
        }

        return $query->get();
    }

    public function getFilteredTotalCount(string $channel): int
    {
        $query = MassMessage::query();

        if ($channel) {
            $query->where('channel', $channel);
        }

        return $query->count();
    }
}
