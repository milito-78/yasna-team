<?php

namespace App\Providers;

use App\Services\Users\Interfaces\IUserRepository;
use App\Services\Users\Interfaces\IUserService;
use App\Services\Users\Repository\UserRepository;
use App\Services\Users\Services\UserService;
use Illuminate\Http\Request;
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
        //baseAuthenticate method
        Request::macro('baseAuthenticate', function () : ?string {
            $header = $this->header('Authorization', '');

            $position = strrpos($header, 'Basic ');

            if ($position !== false) {
                $header = substr($header, $position + 6);

                return str_contains($header, ',') ? strstr($header, ',', true) : $header;
            }
            return null;
        });
    }

    private function registerUser(): void
    {
        //Repositories
        $this->app->bind(IUserRepository::class,UserRepository::class);

        //Services
        $this->app->bind(IUserService::class,UserService::class);

    }
}
