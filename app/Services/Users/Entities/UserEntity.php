<?php

namespace App\Services\Users\Entities;

use App\Models\Enums\UserStatusesEnum;
use App\Models\User;
use Illuminate\Support\Carbon;

class UserEntity
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public UserStatusesEnum $status,
        public Carbon $created_at,
        public Carbon $updated_at,
    )
    {
    }

    public function isBlocked() : bool
    {
        return $this->status == UserStatusesEnum::Block;
    }

    public function toUser() : User
    {
        $user               = new User();
        $user->id           = $this->id;
        $user->name         = $this->name;
        $user->email        = $this->email;
        $user->status       = $this->status;
        $user->status_id    = $this->status->value;
        $user->created_at   = $this->created_at;
        $user->updated_at   = $this->updated_at;
        return $user;
    }

    public function toArray() : array
    {
        return [
            "id"            => $this->id,
            "name"          => $this->name,
            "email"         => $this->email,
            "status"        => $this->status,
            "created_at"    => $this->created_at,
            "updated_at"    => $this->updated_at
        ];
    }
}
