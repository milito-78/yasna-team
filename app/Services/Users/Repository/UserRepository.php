<?php

namespace App\Services\Users\Repository;

use App\Models\User;
use App\Services\Users\Entities\UserEntity;
use App\Services\Users\Interfaces\IUserRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class UserRepository implements IUserRepository
{
    private function query(): Builder
    {
        return User::query();
    }

    public function findUserByEmail(string $email): ?UserEntity
    {
        /**
         * @var User $user
         */
        $user = $this->query()->where("email", "=", $email)->first();

        if (!$user)
            return null;
        return $this->wrapWithEntity($user);
    }

    private function wrapWithEntity(User $user): UserEntity
    {
        return UserEntity::fromUser($user);
    }
}
