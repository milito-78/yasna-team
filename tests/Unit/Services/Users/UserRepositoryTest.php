<?php
namespace Tests\Unit\Services\Users;

use App\Models\Enums\UserStatusesEnum;
use App\Models\User;
use App\Services\Users\Repository\UserRepository;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private function seedDb(): void
    {
        $this->seed([
            UserSeeder::class
        ]);
    }

    /**
     * Test find user by email
     */
    public function test_find_user_by_email(): void
    {
        /**
         * Seed database
         */
        $this->seedDb();

        /**
         * Create special user
         * @var User $seed
         */
        $seed = User::query()->create([
            "name"      => "Test user",
            "email"     => "test@example.com",
            "status_id" => UserStatusesEnum::Active->value
        ]);

        $repository = $this->app->make(UserRepository::class);

        $user = $repository->findUserByEmail("test@example.com");
        $this->assertNotNull($user);
        $this->assertEquals($user->email,$seed->email);
        $this->assertEquals($user->id,$seed->id);
        $this->assertEquals($user->status,$seed->status);
    }

    /**
     * Test find user by email
     */
    public function test_not_found_user()
    {
        /**
         * Seed database
         */
        $this->seedDb();

        $repository = $this->app->make(UserRepository::class);

        $user = $repository->findUserByEmail("test@example.com");
        $this->assertNull($user);
    }

}
