<?php

use App\Http\Middleware\AdminOnly;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\StudentOnly;
use App\Http\Middleware\AdminStudentOnly;
use App\Http\Middleware\MentorOnly;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        // web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function ($middleware) {
        $middleware->alias(['student' => StudentOnly::class]);
    })
    ->withMiddleware(function ($middleware) {
        $middleware->alias(['admin_or_student' => AdminStudentOnly::class]);
    })
    ->withMiddleware(function ($middleware) {
        $middleware->alias(['admin' => AdminOnly::class]);
    })
    ->withMiddleware(function ($middleware) {
        $middleware->alias(['mentor' => MentorOnly::class]);
    })
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
