<?php

namespace App\Exceptions;

use Askync\Utils\Facades\AskyncResponse;
use Askync\Utils\Utils\ResponseException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        if( $e instanceof ResponseException) {
            return AskyncResponse::fail($e->statusCode, $e->getMessage(), $e->errors);
        }
        if ($e instanceof AuthenticationException) {
            return AskyncResponse::fail(401, 'Unauthorized');
        }
        if ($request->expectsJson()) {
            if($e instanceof NotFoundHttpException) {
                return AskyncResponse::fail(404, 'Resource doesn\'t exists');
            }
        }

        return parent::render($request, $e);
    }
}
