<?php

namespace App\Repositories;

use App\Models\MessageDeliveryLog;

class MessageDeliveryLogRepository
{

    public function create(array $data): MessageDeliveryLog
    {
        return MessageDeliveryLog::query()->create($data);
    }
}
