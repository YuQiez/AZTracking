<?php

use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StatusController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login']);

use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\CustomerController;



Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);
    
    // User routes
    Route::prefix('users')->controller(UserController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/store', 'store');
        Route::get('/{id}', 'show');
        Route::post('/{id}/update', 'update');
        Route::delete('/{id}/delete', 'destroy');
    });

    // Role routes
    Route::prefix('roles')->controller(RoleController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/store', 'store');
        Route::get('/{id}', 'show');
        Route::post('/{id}/update', 'update');
        Route::delete('/{id}/delete', 'destroy');
    });

    // Permission routes
    Route::prefix('permissions')->controller(PermissionController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/store', 'store');
        Route::get('/{id}', 'show');
        Route::post('/{id}/update', 'update');
        Route::delete('/{id}/delete', 'destroy');
    });

    // Customer routes
    Route::prefix('customers')->controller(CustomerController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/store', 'store');
        Route::get('/{id}', 'show');
        Route::post('/{id}/update', 'update');
        Route::delete('/{id}/delete', 'destroy');
});

    Route::prefix('statuses')->controller(StatusController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/store', 'store');
        Route::get('/{id}', 'show');
        Route::post('/{id}/update', 'update');
        Route::delete('/{id}/delete', 'destroy');
    
});

Route::prefix('feedbacks')->controller(FeedbackController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/store', 'store');
        Route::get('/{id}', 'show');
        Route::post('/{id}/update', 'update');
        Route::delete('/{id}/delete', 'destroy');
    });

    Route::prefix('orders')->controller(OrderController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/store', 'store');
        Route::get('/{id}', 'show');
        Route::post('/{id}/update', 'update');
        Route::delete('/{id}/delete', 'destroy');
    });

});


