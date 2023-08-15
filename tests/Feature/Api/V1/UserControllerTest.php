<?php

namespace Tests\Feature\Api\V1;

use App\Models\User;
use App\Services\Users\Interfaces\IUserService;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    private function seedDb()
    {
        $this->seed([
            UserSeeder::class
        ]);
    }

    /**
     * Test get profile successfully
     */
    public function test_get_profile_successfully(): void
    {
        $this->seedDb();
        /**
         * @var User $user
         */
        $user = User::query()->activeUser()->first();

        /**
         * Mock UserService
         */
        $this->mock(IUserService::class,function (MockInterface $mock) use($user){
            return $mock->shouldReceive("GetUserByEmail")->withArgs([
                $user->email
            ])->andReturn($user->toEntity());
        });

        $response = $this->withToken($user->email,"Basic")->get('/api/v1/users/profile');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            "message",
            "success",
            "data" => [
                "id",
                "name",
                "email",
                "created_at",
                "updated_at",
            ]
        ]);
        $response->assertJson([
            "data" => [
                "id"         => $user->id,
                "name"       => $user->name,
                "email"      => $user->email,
                "status"     => $user->status->value,
                "created_at" => $user->created_at->toISOString(),
                "updated_at" => $user->updated_at->toISOString(),
            ]
        ]);
    }

    /**
     * Test invalid token
     */
    public function test_invalid_token_error(): void
    {
        /**
         * Mock UserService
         */
        $this->mock(IUserService::class,function (MockInterface $mock){
            return $mock->shouldReceive("GetUserByEmail")->withArgs([
                "test@example.com"
            ])->andReturnNull();
        });

        $response = $this->withToken("test@example.com","Basic")->get('/api/v1/users/profile');
        $response->assertStatus(401);
    }

    /**
     * Test unauthenticated
     */
    public function test_get_unauthenticated_error(): void
    {
        /**
         * Mock UserService
         */
        $this->mock(IUserService::class,function (MockInterface $mock){
            return $mock->shouldNotHaveReceived("GetUserByEmail");
        });

        $response = $this->get('/api/v1/users/profile');
        $response->assertStatus(401);
    }


    /**
     * Test blocked user
     */
    public function test_blocked_user(): void
    {
        $this->seedDb();
        /**
         * @var User $user
         */
        $user = User::query()->blockedUser()->first();

        /**
         * Mock UserService
         */
        $this->mock(IUserService::class,function (MockInterface $mock) use($user){
            return $mock->shouldReceive("GetUserByEmail")->withArgs([
                $user->email
            ])->andReturn($user->toEntity());
        });

        $response = $this->withToken($user->email,"Basic")->get('/api/v1/users/profile');
        $response->assertStatus(403);
    }

}
