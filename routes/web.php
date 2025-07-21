<?php

use App\Http\Controllers\Web\LoginController;
use Illuminate\Support\Facades\Route;

Route::controller(LoginController::class)->group(function () {
    Route::post('telescope-login', 'login');
    Route::get('telescope-login', 'showLoginForm')->name('login');
});
