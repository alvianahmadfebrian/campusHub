<?php

use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\RedirectIfSupabaseAuthenticated;
use App\Http\Middleware\SupabaseAdminMiddleware;
use App\Http\Middleware\SupabaseAuthMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            HandleInertiaRequests::class,
        ]);

        $middleware->alias([
            'supabase.auth' => SupabaseAuthMiddleware::class,
            'guest.supabase' => RedirectIfSupabaseAuthenticated::class,
            'supabase.admin' => SupabaseAdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
