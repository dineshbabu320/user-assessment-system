<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Sentry\Laravel\Integration;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'hasExamToken' => \App\Http\Middleware\HasExamToken::class,
            'subdivisionStaff' => \App\Http\Middleware\SubdivisionStaff::class,
            'examiner' => \App\Http\Middleware\Examiner::class,
            'admin' => \App\Http\Middleware\Admin::class,
            'canViewStudentProfile' => \App\Http\Middleware\CanViewStudentProfile::class,
            'mentor' => \App\Http\Middleware\Mentor::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        Integration::handles($exceptions);
    })->create();
