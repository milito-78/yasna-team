<?php

namespace App\Services\Users\Entities;

use App\Models\Enums\UserStatusesEnum;
use App\Models\User;
use Illuminate\Support\Carbon;

class UserEntity
{
    public function __construct(
        public int $id,
        public string $email,
        public UserStatusesEnum $status,
        public Carbon $created_at,
        public Carbon $updated_at,
    )
    {
    }

    public static function fromUser(User $user): self
    {
        return new self(
            $user->id,
            $user->email,
            $user->status,
            $user->created_at,
            $user->updated_at
        );
    }

    public function toArray() : array
    {
        return [
            "id"            => $this->id,
            "email"         => $this->email,
            "status"        => $this->status,
            "created_at"    => $this->created_at,
            "updated_at"    => $this->updated_at
        ];
    }
}
