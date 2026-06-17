<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{

    public function find(int $id): ?User
    {
        return User::query()->find($id);
    }

    public function findByPhone(string $phone): ?User
    {
        return User::query()->where('phone', $phone)->first();
    }

    public function create(array $data): User
    {
        return User::query()->create($data);
    }
}
