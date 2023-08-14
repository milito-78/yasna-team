<?php

namespace App\Services\Users\Services;

use App\Services\Users\Entities\UserEntity;
use App\Services\Users\Interfaces\IUserRepository;
use App\Services\Users\Interfaces\IUserService;

class UserService implements IUserService
{

    public function __construct(
        private readonly IUserRepository $userRepository
    )
    {
    }

    public function GetUserByEmail(string $email): ?UserEntity
    {
        return $this->userRepository->findUserByEmail($email);
    }
}
