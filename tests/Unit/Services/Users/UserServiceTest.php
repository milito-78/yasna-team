<?php

namespace Tests\Unit\Services\Users;



use App\Models\Enums\UserStatusesEnum;
use App\Services\Users\Entities\UserEntity;
use App\Services\Users\Services\UserService;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\Unit\Services\Users\Mock\UserRepositoryMock;

class UserServiceTest extends TestCase
{

    private function getData(Carbon $time):array{
        return [
            [
                'id' => 1,
                'name' => 'Test User',
                'email' => 'test@example.com',
                'status' => UserStatusesEnum::Active,
                'created_at' => $time,
                'updated_at' => $time,
            ],
            [
                'id' => 2,
                'name' => 'Test User 2',
                'email' => 'test2@example.com',
                'status' => UserStatusesEnum::Active,
                'created_at' => $time,
                'updated_at' => $time,
            ],
        ];
    }

    /**
     * Test get user by email successfully
     */
    public function test_get_user_by_email()
    {
        $time = now();
        $userService = new UserService(new UserRepositoryMock($this->getData($time)));

        $user = $userService->GetUserByEmail('test@example.com');
        $this->assertNotNull($user);
        $this->assertInstanceOf(UserEntity::class, $user);
        $this->assertEquals([
            'id' => 1,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'status' => UserStatusesEnum::Active,
            'created_at' => $time,
            'updated_at' => $time,
        ], $user->toArray());
    }

    /**
     * Test Can't find user by email
     */
    public function test_cannot_find_user()
    {
        $userService = new UserService(new UserRepositoryMock([]));

        $user = $userService->GetUserByEmail('test@example.com');
        $this->assertNull($user);
        $this->assertNotInstanceOf(UserEntity::class, $user);
    }
}
