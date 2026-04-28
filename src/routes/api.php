<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegistrationController;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');
    Route::prefix('registration')->group(function () {
        Route::post('student', [RegistrationController::class, 'registerStudent']);
        Route::post('company', [RegistrationController::class, 'registerCompany']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        // TODO
    });
});

