<?php

use App\Http\Controllers\CardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::post('registration', 'register');
    Route::post('login', 'login')->middleware('throttle:10,1');
});

Route::post('/webhook/transactions', [TransactionController::class, 'handleWebhook']);

Route::group(['middleware' => 'auth:api'], function () {
    Route::controller(CardController::class)->group(function () {
        Route::post('cards', 'issue');
        Route::get('cards/{id}', 'get')->whereNumber('id');
        Route::get('cards', 'search');
        Route::put('cards/{id}/freeze', 'freeze')->whereNumber('id');
        Route::put('cards/{id}/unfreeze', 'unfreeze')->whereNumber('id');
        Route::post('card/{id}/reissue', 'reissue')->whereNumber('id');
    });
    Route::controller(UserController::class)->group(function () {
        Route::get('profile', 'profile');
    });
    Route::controller(TransactionController::class)->group(function () {
        Route::get('transactions/{id}', 'get')->whereNumber('id');
    });
});
