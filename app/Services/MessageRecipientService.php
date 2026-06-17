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
}
