<?php

namespace App\Services;

use App\Models\MessageDeliveryLog;
use App\Repositories\MessageDeliveryLogRepository;

class MessageDeliveryLogService
{
    public function __construct(
        private readonly MessageDeliveryLogRepository $repository,
    ) {}
    
    public function create(array $data): MessageDeliveryLog
    {
        return $this->repository->create($data);
    }
}
