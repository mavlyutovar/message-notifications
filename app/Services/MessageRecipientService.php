<?php

namespace App\Services;

use App\Data\MassMessageRecipientData;
use App\Models\MessageRecipient;
use App\Repositories\MessageRecipientRepository;

class MessageRecipientService
{
    public function __construct(
        private readonly MessageRecipientRepository $repository,
    ) {}

    public function create(MassMessageRecipientData $data): MessageRecipient
    {
        return $this->repository->create($data);
    }

    public function find(int $id): ?MessageRecipient
    {
        return $this->repository->find($id);
    }

    public function insertMany(array $data):void
    {
        $this->repository->insert($data);
    }

    public function updateStatus(int $id, string $status, string $lastError = null): void
    {
        $this->repository->updateStatus($id, $status, $lastError);
    }

    public function updateStatuses(array $ids, string $status): void
    {
        $this->repository->updateStatuses($ids, $status);
    }

    public function incrementAttempts(int $id): void
    {
        $this->repository->incrementAttempts($id);
    }
}
