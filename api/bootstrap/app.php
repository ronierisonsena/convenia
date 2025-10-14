<?php

use App\Http\Middleware\CheckApiKey;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Laravel\Passport\Exceptions\AuthenticationException;
use Laravel\Passport\Exceptions\MissingScopeException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

use Illuminate\Auth\AuthenticationException as AuthException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(CheckApiKey::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                [$status, $message] = match (true) {
                    $e instanceof NotFoundHttpException,
                    $e instanceof RouteNotFoundException => [Response::HTTP_NOT_FOUND, 'Resource not found.'],
                    $e instanceof ValidationException => [Response::HTTP_UNPROCESSABLE_ENTITY, $e->errors()],
                    $e instanceof AuthenticationException => [Response::HTTP_FORBIDDEN, 'Unauthenticated or privileges missing.'],
                    $e instanceof AuthException => [Response::HTTP_UNAUTHORIZED, 'Unauthenticated.'],
                    $e instanceof MissingScopeException,
                    $e instanceof AccessDeniedHttpException => [Response::HTTP_FORBIDDEN, $e->getMessage()],
                    default => [Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage()],
                };

                Log::error($e->getMessage(), [
                    'message' => $message,
                    'exception' => get_class($e),
                    'trace' => collect($e->getTrace())->take(5),
                ]);

                $response['message'] = 'Error processing request.';

                // Add exception and trace for dev
                if (! app()->environment('production')) {
                    $response['message'] = $message;
                    $response['exception'] = get_class($e);
                    $response['trace'] = collect($e->getTrace())->take(3);
                }

                return response()->json($response, $status);
            }
        });

        $exceptions->respond(function ($response, Throwable $e) {
            return $response;
        });
    })->create();
