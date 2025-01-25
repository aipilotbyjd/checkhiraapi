<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\HomeController;
use App\Http\Controllers\Api\v1\WorkController;
use App\Http\Controllers\Api\v1\WorkItemController;
use App\Http\Controllers\Api\v1\PaymentController;

//v1 api routes
Route::prefix('v1')->group(function () {
    //auth routes
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::post('phone-login', [AuthController::class, 'phoneLogin'])->name('phone-login');
    Route::post('verify-otp', [AuthController::class, 'verifyOtp'])->name('verify-otp');
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');
    Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('reset-password');

    //api middleware
    Route::group(['middleware' => 'auth:api'], function () {
        //user routes
        Route::get('user', [AuthController::class, 'user'])->name('user');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('profile', [AuthController::class, 'profile'])->name('profile');
        Route::put('profile', [AuthController::class, 'updateProfile'])->name('profile.update');

        //work routes
        Route::prefix('works')->group(function () {
            Route::get('/', [WorkController::class, 'index'])->name('works.index');
            Route::post('/', [WorkController::class, 'store'])->name('works.store');
            Route::get('/{id}', [WorkController::class, 'details'])->name('works.details');
            Route::put('/{id}', [WorkController::class, 'update'])->name('works.update');
            Route::delete('/{id}', [WorkController::class, 'destroy'])->name('works.destroy');
        });

        //work item routes
        Route::prefix('work-items')->group(function () {
            Route::get('/', [WorkItemController::class, 'index'])->name('work-items.index');
            Route::post('/', [WorkItemController::class, 'store'])->name('work-items.store');
            Route::get('/{id}', [WorkItemController::class, 'details'])->name('work-items.details');
            Route::put('/{id}', [WorkItemController::class, 'update'])->name('work-items.update');
            Route::delete('/{id}', [WorkItemController::class, 'destroy'])->name('work-items.destroy');
        });

        //payment routes
        Route::prefix('payments')->group(function () {
            Route::get('/sources', [PaymentController::class, 'paymentSources'])->name('payments.sources');
            Route::get('/', [PaymentController::class, 'index'])->name('payments.index');
            Route::post('/', [PaymentController::class, 'store'])->name('payments.store');
            Route::get('/{id}', [PaymentController::class, 'details'])->name('payments.details');
            Route::put('/{id}', [PaymentController::class, 'update'])->name('payments.update');
            Route::delete('/{id}', [PaymentController::class, 'destroy'])->name('payments.destroy');
        });

        //get all settings
        Route::get('settings', [HomeController::class, 'settings'])->name('settings');

        //get all notifications
        Route::get('notifications', [HomeController::class, 'notifications'])->name('notifications');
    });
});
