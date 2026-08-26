<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiIsEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('app.api_enabled', true)) {
            return response()->json([
                'success' => false,
                'message' => 'API sedang dinonaktifkan.',
            ], 503);
        }

        return $next($request);
    }
}
