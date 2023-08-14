<?php

namespace App\Services\Users\Interfaces;

use App\Services\Users\Entities\UserEntity;

interface IUserService
{
    /**
     * Get user by email.
     * If user doesn't exist, returned result will be null.
     *
     * @param string $email
     * @return UserEntity|null
     */
    public function GetUserByEmail(string $email) : ?UserEntity;
}
