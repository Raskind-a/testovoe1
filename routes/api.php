<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        Route::prefix('events')->name('events.')->group(function () {
            Route::get('', [EventController::class, 'index'])->name('index');
            Route::post('', [EventController::class, 'store'])->name('create');

            Route::prefix('{event}')->group(function () {
                Route::get('', [EventController::class, 'show'])->name('show');
                Route::post('/participate', [EventController::class, 'participate']);
                Route::post('/cancellation', [EventController::class, 'cancellation']);

                Route::delete('', [EventController::class, 'delete'])->name('delete');
//                Route::get('/participants', [EventController::class, 'getParticipants']);
            });
        });

    });

    //web and guest
    Route::middleware('guest')->group(function () {
        Route::post('/login', [AuthController::class, 'authenticate'])->name('login');
        Route::post('/register', [UserController::class, 'store'])->name('register');
    });
    // todo logout
    // методы событий events
});
