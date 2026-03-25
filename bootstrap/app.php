<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'patient' => \App\Http\Middleware\EnsurePatient::class,
            'admin' => \App\Http\Middleware\EnsureAdmin::class,
        ]);
        $middleware->redirectUsersTo(function () {
            $webUser = auth('web')->user();
            if ($webUser instanceof \App\Models\User && $webUser->isPatient()) {
                return route('musteri.panel');
            }

            $adminUser = auth('admin')->user();
            if ($adminUser instanceof \App\Models\User && $adminUser->isAdmin()) {
                return route('admin.panel');
            }

            return '/';
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
