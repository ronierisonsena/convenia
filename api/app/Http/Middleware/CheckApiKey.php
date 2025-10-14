<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response as JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CheckApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (
            $request->is('api/documentation/*') ||
            $request->is('api/documentation') ||
            $request->is('docs')) {
            return $next($request);
        }
        $path = $request->path();

        if (
            str_starts_with($path, 'docs') ||
            str_starts_with($path, 'api/documentation')
        ) {
            return $next($request);
        }

        if ($request->header('api-key') !== env('API_KEY')) {
            return response()->json([
                'message' => 'Unauthorized.',
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
