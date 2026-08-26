<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

$routing = [
    'web' => __DIR__.'/../routes/web.php',
    'commands' => __DIR__.'/../routes/console.php',
    'health' => '/up',
];

// Matikan API: set API_ENABLED=false di .env lalu php artisan config:clear
if (filter_var(env('API_ENABLED', true), FILTER_VALIDATE_BOOLEAN)) {
    $routing['api'] = __DIR__.'/../routes/api.php';
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(...$routing)
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureUserRole::class,
            'approved' => \App\Http\Middleware\EnsureUserIsApproved::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(fn (Request $request) => $request->is('api/*') || $request->expectsJson());
    })->create();
