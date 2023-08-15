<?php

namespace App\Models;

use App\Models\Enums\UserStatusesEnum;
use App\Services\Users\Entities\UserEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property int $status_id
 * @property UserStatusesEnum $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'status_id',
    ];

    protected $appends = [
        "status"
    ];


    public function getStatusAttribute() : UserStatusesEnum
    {
        return UserStatusesEnum::from($this->status_id);
    }


    public function toEntity() : UserEntity
    {
        return new UserEntity(
            $this->id,$this->name,$this->email,$this->status,$this->created_at,$this->updated_at
        );
    }

    public static function fromEntity(UserEntity $userEntity) : User
    {
        $user               = new User();
        $user->id           = $userEntity->id;
        $user->name         = $userEntity->name;
        $user->email        = $userEntity->email;
        $user->status       = $userEntity->status;
        $user->status_id    = $userEntity->status->value;
        $user->created_at   = $userEntity->created_at;
        $user->updated_at   = $userEntity->updated_at;
        return $user;
    }

}
