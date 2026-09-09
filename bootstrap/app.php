<?php

use App\Support\ApiErrorCode;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'capability' => \App\Http\Middleware\RequireCapability::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $isApi = fn (Request $request): bool => $request->is('api/*');

        $exceptions->render(function (AuthenticationException $exception, Request $request) use ($isApi) {
            return $isApi($request)
                ? api_response()->error('Unauthenticated.', ApiErrorCode::Unauthenticated, 401)
                : null;
        });

        $exceptions->render(function (AuthorizationException $exception, Request $request) use ($isApi) {
            return $isApi($request)
                ? api_response()->error($exception->getMessage() ?: 'Forbidden.', ApiErrorCode::Forbidden, 403)
                : null;
        });

        $exceptions->render(function (Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $exception, Request $request) use ($isApi) {
            return $isApi($request)
                ? api_response()->error($exception->getMessage() ?: 'Forbidden.', ApiErrorCode::Forbidden, 403)
                : null;
        });

        $exceptions->render(function (ValidationException $exception, Request $request) use ($isApi) {
            if (! $isApi($request)) {
                return null;
            }

            $code = $exception->errorBag === ApiErrorCode::InvalidCredentials->value
                ? ApiErrorCode::InvalidCredentials
                : ApiErrorCode::ValidationFailed;

            return api_response()->error($exception->getMessage(), $code, 422, $exception->errors());
        });

        $exceptions->render(function (NotFoundHttpException $exception, Request $request) use ($isApi) {
            return $isApi($request)
                ? api_response()->error('Resource not found.', ApiErrorCode::NotFound, 404)
                : null;
        });

        $exceptions->render(function (MethodNotAllowedHttpException $exception, Request $request) use ($isApi) {
            return $isApi($request)
                ? api_response()->error('Method not allowed.', ApiErrorCode::MethodNotAllowed, 405)
                : null;
        });

        $exceptions->render(function (TooManyRequestsHttpException $exception, Request $request) use ($isApi) {
            if (! $isApi($request)) {
                return null;
            }

            $retryAfter = (int) ($exception->getHeaders()['Retry-After'] ?? 60);

            return api_response()->error(
                'Too many requests.',
                ApiErrorCode::RateLimited,
                429,
                extra: ['retry_after' => $retryAfter],
            )->withHeaders(['Retry-After' => (string) $retryAfter]);
        });

        $exceptions->render(function (\Throwable $exception, Request $request) use ($isApi) {
            return $isApi($request)
                ? api_response()->error('Internal server error.', ApiErrorCode::InternalError, 500)
                : null;
        });
    })->create();
