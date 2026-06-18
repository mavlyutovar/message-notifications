<?php

namespace App\Repositories;

use App\Data\MassMessageRecipientData;
use App\Models\MessageRecipient;

class MessageRecipientRepository
{
    public function create(MassMessageRecipientData $data): MessageRecipient
    {
        return MessageRecipient::query()->create([
            'mass_message_id' => $data->mass_message_id,
            'user_id' => $data->user_id,
            'status' => $data->status,
            'attempts' => $data->attempts,
            'last_error' => $data->last_error,
        ]);
    }

    public function find(int $id): ?MessageRecipient
    {
        return MessageRecipient::query()->with(['user', 'massMessage'])->find($id);
    }

    public function insert(array $rows): void
    {
        MessageRecipient::query()->insert($rows);
    }

    public function updateStatus(int $id, string $status, string $lastError = null): void
    {
        $model = MessageRecipient::query()->findOrFail($id);
        $model->update(['status' => $status]);

        if (isset($lastError)) {
            $model->update(['last_error' => $lastError]);
        }
    }

    public function updateStatuses(array $ids, string $status): void
    {
        MessageRecipient::query()->whereIn('id', $ids)->update(['status' => $status]);
    }

    public function incrementAttempts(int $id): void
    {
        MessageRecipient::query()->where('id', $id)
            ->increment('attempts');
    }
}
