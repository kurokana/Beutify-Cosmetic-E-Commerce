<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register middleware aliases so they can be referenced by name in
        // route definitions.
        $middleware->alias([
            'check.account.active' => \App\Http\Middleware\CheckAccountActive::class,
            // Requirement 14.5: verify admin role before allowing access to admin routes
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            // Requirement 14.2: Force HTTPS in production
            'force.https' => \App\Http\Middleware\ForceHttps::class,
        ]);

        // Append CheckAccountActive to the 'web' middleware group so that
        // every authenticated web request is checked (Requirement 12.2).
        $middleware->appendToGroup('web', \App\Http\Middleware\CheckAccountActive::class);

        // Append ForceHttps to the 'web' middleware group (Requirement 14.2)
        $middleware->appendToGroup('web', \App\Http\Middleware\ForceHttps::class);

        // Exclude Midtrans webhook from CSRF protection (Requirement 5.6)
        $middleware->validateCsrfTokens(except: [
            'api/webhook/midtrans',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
