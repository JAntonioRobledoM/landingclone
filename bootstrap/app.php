<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckArtistRole;
use App\Http\Middleware\CheckUserRole;
use App\Http\Middleware\CheckRole;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
        'role.artist' => CheckArtistRole::class,
        'role.user' => CheckUserRole::class,
        'role' => CheckRole::class, 
    ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();