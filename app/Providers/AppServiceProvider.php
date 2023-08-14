<?php

namespace App\Providers;

use App\Services\Users\Interfaces\IUserRepository;
use App\Services\Users\Interfaces\IUserService;
use App\Services\Users\Repository\UserRepository;
use App\Services\Users\Services\UserService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerUser();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

    private function registerUser(): void
    {
        //Repositories
        $this->app->bind(IUserRepository::class,UserRepository::class);

        //Services
        $this->app->bind(IUserService::class,UserService::class);

    }
}
