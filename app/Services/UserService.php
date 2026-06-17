<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;

class UserService
{
    public function __construct(
        private readonly UserRepository $repository,
    ) {}

    public function find(int $id): ?User
    {
        return $this->repository->find($id);
    }

    public function findByPhone(string $phone): ?User
    {
        return $this->repository->findByPhone($phone);
    }
}
