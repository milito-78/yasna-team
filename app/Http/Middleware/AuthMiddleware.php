<?php

namespace App\Http\Middleware;

use App\Services\Users\Interfaces\IUserService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    public function __construct(
        private readonly IUserService $service
    )
    {
    }


    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->baseAuthenticate();
        if (!$token)
            abort(401);

        $user = $this->service->GetUserByEmail($token);
        if (!$user)
            abort(401);

        if ($user->isBlocked()) {
            abort(403);
        }

        auth()->setUser($user->toUser());

        return $next($request);
    }
}
