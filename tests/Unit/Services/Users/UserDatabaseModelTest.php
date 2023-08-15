<?php

namespace Tests\Unit\Services\Users;

use App\Models\Enums\UserStatusesEnum;
use App\Models\User;
use App\Services\Users\Entities\UserEntity;
use Tests\TestCase;

class UserDatabaseModelTest extends TestCase
{
    /**
     * Test status property
     */
    public function test_get_status_attribute()
    {
        $user = new User([
            'status_id' => UserStatusesEnum::Active->value,
        ]);

        $status = $user->status;

        $this->assertInstanceOf(UserStatusesEnum::class, $status);
        $this->assertEquals(UserStatusesEnum::Active, $status);
    }

    /**
     * Test convert User model to UserEntity
     */
    public function test_to_entity()
    {
        $user = new User([
            'name'       => 'John Doe',
            'email'      => 'john@example.com',
            'status_id'  => UserStatusesEnum::Active->value,
        ]);
        $user->id = 1;
        $user->created_at = now();
        $user->updated_at = now();

        $userEntity = $user->toEntity();

        $this->assertInstanceOf(UserEntity::class, $userEntity);
        $this->assertEquals($user->id, $userEntity->id);
        $this->assertEquals($user->name, $userEntity->name);
        $this->assertEquals($user->email, $userEntity->email);
        $this->assertEquals($user->status, $userEntity->status);
    }

    /**
     * Test create User from UserEntity
     */
    public function test_from_entity()
    {
        $userEntity = new UserEntity(
            1,
            'John Doe',
            'john@example.com',
            UserStatusesEnum::Active,
            now(),
            now()
        );

        $user = User::fromEntity($userEntity);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($user->id, $userEntity->id);
        $this->assertEquals($user->name, $userEntity->name);
        $this->assertEquals($user->email, $userEntity->email);
        $this->assertEquals($user->status, $userEntity->status);
    }
}
