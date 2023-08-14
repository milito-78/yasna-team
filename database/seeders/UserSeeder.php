<?php

namespace Database\Seeders;

use App\Models\Enums\UserStatusesEnum;
use App\Models\User;
use App\Models\UserStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (UserStatusesEnum::cases() as $status){
            UserStatus::query()->firstOrCreate([
                "id" => $status->value,
                "title" => $status->name
            ],[
                "id" => $status->value,
                "title" => $status->name
            ]);
        }

        $users = $this->getUsersList();
        foreach ($users as $user){
            User::query()->updateOrCreate([
                "name" => $user["name"],
                "email" => $user["email"],
            ],[
                "name" => $user["name"],
                "email" => $user["email"],
                "status_id" => $user["status_id"]
            ]);
        }
    }

    private function getUsersList():array{
        return  [
            [
                "name" => "Blocked User",
                "email" => "blocked.user@yasnateam.com",
                "status_id" => UserStatusesEnum::Block
            ],
            [
                "name" => "Active User",
                "email" => "active.user@yasnateam.com",
                "status_id" => UserStatusesEnum::Active
            ]
        ];
    }
}
