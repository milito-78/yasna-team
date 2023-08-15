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

        //Todo change response to a unique structure
        return new UserResource($user);
    }
}
