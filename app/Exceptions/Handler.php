<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }


    public function render($request, Throwable $e)
    {
        $code       = $e->getCode();
        $message    = $e->getMessage();

        if ($e instanceof HttpException){
            $code       = $e->getStatusCode();
            $message    = $e->getMessage();
        }

        if ($e instanceof NotFoundHttpException || $e instanceof ModelNotFoundException) {
            return  failed_json()->code(404)->message("Not found")->send();
        }

        if ($e instanceof ValidationException ) {
            return failed_json()->code(422)->message("Not found")->errors($e->errors())->send();
        }

        if ($e instanceof AuthenticationException || $code == 401 ) {
            return failed_json()->code(401)->message("Un authenticate")->send();
        }

        if ($e instanceof AuthorizationException || $code == 403 ) {
            return failed_json()->code(403)->message("Un authorized")->send();
        }

        if ($e->getCode() == 500 || $e->getCode() < 100 || $e->getCode() >= 600){
            $message = "Internal error";
            $code = 500;
        }

        return failed_json()->code($code)->message($message)->send();
    }

}
