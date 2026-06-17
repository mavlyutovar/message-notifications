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
}
