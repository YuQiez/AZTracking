<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        
        // 1. Spatie Permission Aliases
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

        // 2. THE FIX: Stop Laravel from redirecting API users to a login page
        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('api/*')) {
                // Returning null forces Laravel to throw a clean 401 JSON error 
                // instead of looking for a login route.
                return null; 
            }
            // For normal web routes, try to go to login (if it exists)
            return route('login'); 
        });

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();