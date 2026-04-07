<?php

use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\EnsurePatient;
use App\Models\User;
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
            'patient' => EnsurePatient::class,
            'admin' => EnsureAdmin::class,
        ]);
        $middleware->redirectUsersTo(function () {
            $patientUser = auth('patient')->user();
            if ($patientUser instanceof User && $patientUser->isPatient()) {
                return route('musteri.panel');
            }

            $adminUser = auth('admin')->user();
            if ($adminUser instanceof User && $adminUser->isAdmin()) {
                return route('admin.panel');
            }

            return '/';
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
