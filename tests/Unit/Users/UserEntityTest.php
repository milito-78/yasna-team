<?php

namespace Tests\Unit\Users;

use App\Models\Enums\UserStatusesEnum;
use App\Services\Users\Entities\UserEntity;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\TestCase;

class UserEntityTest extends TestCase
{

    /**
     * Test user is blocked
     */
    public function testIsBlocked()
    {
        $userEntity = new UserEntity(
            1,
            'Test User',
            'test@example.com',
            UserStatusesEnum::Block,
            Carbon::now(),
            Carbon::now()
        );

        $this->assertTrue($userEntity->isBlocked());
    }

    /**
     * Test toArray function
     */
    public function testToArray()
    {
        $createdAt = Carbon::now();
        $updatedAt = Carbon::now()->addHour();

        $userEntity = new UserEntity(
            1,
            'Test User',
            'test@example.com',
            UserStatusesEnum::Active,
            $createdAt,
            $updatedAt
        );

        $expectedArray = [
            "id"            => 1,
            "name"          => 'Test User',
            "email"         => 'test@example.com',
            "status"        => UserStatusesEnum::Active,
            "created_at"    => $createdAt,
            "updated_at"    => $updatedAt
        ];

        $this->assertEquals($expectedArray, $userEntity->toArray());
    }

}
