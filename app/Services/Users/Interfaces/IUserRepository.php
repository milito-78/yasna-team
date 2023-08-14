<?php

namespace App\Services\Users\Interfaces;


use App\Services\Users\Entities\UserEntity;

interface IUserRepository
{
    /**
     * Find user by email
     *
     * @param string $email
     * @return UserEntity|null
     */
    public function findUserByEmail(string $email): ?UserEntity;
}
