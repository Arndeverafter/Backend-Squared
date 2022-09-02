<?php

namespace App\Exceptions;

use App\Helpers\FeedBackHelper;
use App\Helpers\UtilsHelper;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Render an exception into an HTTP response.
     *
     * @param  Request  $request
     * @param  Throwable  $e
     * @return Response
     *
     * @throws Throwable
     */
    public function render($request, Throwable $e): Response
    {
        // if something is not found just return this response
        if ($e instanceof NotFoundHttpException && $request->wantsJson()) {
            return UtilsHelper::apiResponseConstruct('message',  FeedBackHelper::NOT_FOUND, $e->getStatusCode());
        }
        // Catch All method not allowed exceptions and just return a nice message
        if ($e instanceof MethodNotAllowedHttpException) {
            return UtilsHelper::apiResponseConstruct('message', $e->getMessage(), $e->getStatusCode());
        }

        if ($e instanceof ModelNotFoundException && $request->wantsJson()) {
            return UtilsHelper::apiResponseConstruct('message',  $e->getMessage(), $e->getCode());
        }
        // CSRF Exceptions
        if ($e instanceof TokenMismatchException && $request->wantsJson()) {
            return UtilsHelper::apiResponseConstruct('message',  $e->getMessage(), 419);
        }

        if ($e instanceof ThrottleRequestsException && $request->wantsJson()) {
            return UtilsHelper::apiResponseConstruct('message',  $e->getMessage(), $e->getCode());
        }

        if ($e instanceof UniqueConstraintViolationException && $request->wantsJson()) {
            return UtilsHelper::apiResponseConstruct('message',  $e->getMessage(), 490);
        }

        if ($e instanceof UniqueConstraintViolationException && $request->wantsJson()) {
            return UtilsHelper::apiResponseConstruct('message',  $e->getMessage(), 490);
        }

        return parent::render($request, $e);
    }
}
