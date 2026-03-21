<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register Spatie permission middleware aliases
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // 401 — Unauthenticated (missing or invalid Sanctum token)
        $exceptions->render(function (AuthenticationException $e, Request $request): JsonResponse {
            return response()->json([
                'message' => 'Unauthenticated. Please provide a valid API token.',
                'error'   => 'authentication_required',
            ], 401);
        });

        // 403 — Forbidden (Gate / Policy denial)
        $exceptions->render(function (AccessDeniedHttpException $e, Request $request): JsonResponse {
            return response()->json([
                'message' => $e->getMessage() ?: 'You do not have permission to perform this action.',
                'error'   => 'authorization_failed',
            ], 403);
        });

        // 404 — Not Found (route model binding or abort(404))
        $exceptions->render(function (NotFoundHttpException $e, Request $request): JsonResponse {
            return response()->json([
                'message' => 'The requested resource was not found.',
                'error'   => 'not_found',
            ], 404);
        });

        // 422 — Validation Error
        $exceptions->render(function (ValidationException $e, Request $request): JsonResponse {
            return response()->json([
                'message' => 'The given data was invalid.',
                'error'   => 'validation_failed',
                'errors'  => $e->errors(),
            ], 422);
        });

        // 500 — Catch-all server error (hides details in production)
        $exceptions->render(function (Throwable $e, Request $request): JsonResponse {
            $debug = config('app.debug');

            return response()->json([
                'message' => $debug ? $e->getMessage() : 'An unexpected server error occurred. Please try again later.',
                'error'   => 'server_error',
                ...($debug ? [
                    'exception' => get_class($e),
                    'file'      => $e->getFile(),
                    'line'      => $e->getLine(),
                    'trace'     => collect($e->getTrace())->take(10)->toArray(),
                ] : []),
            ], 500);
        });

    })->create();