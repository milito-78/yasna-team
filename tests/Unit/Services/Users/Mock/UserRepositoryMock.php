<?php

namespace Tests\Unit\Services\Users\Mock;

use App\Services\Users\Entities\UserEntity;
use App\Services\Users\Interfaces\IUserRepository;

class UserRepositoryMock implements IUserRepository
{
    protected array $userData = [];

    public function __construct(array $userData)
    {
        $this->userData = $userData;
    }

    public function findUserByEmail(string $email): ?UserEntity
    {
        foreach ($this->userData as $user) {
            if ($user['email'] === $email) {
                return new UserEntity(
                    $user['id'],
                    $user['name'],
                    $user['email'],
                    $user['status'],
                    $user['created_at'],
                    $user['updated_at']
                );
            }
        }

        return null;
    }
}
