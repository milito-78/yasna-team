<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Users\UserResource;


class UserController extends Controller
{
    public function __construct(
    )
    {
    }

    public function profile()
    {
        $user = auth()->user();

        return success_json()
                ->succeeded()
                ->message("Success")
                ->data( new UserResource($user))
                ->send();
    }
}
