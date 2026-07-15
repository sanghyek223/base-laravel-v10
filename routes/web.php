<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// main
Route::controller(\App\Http\Controllers\Main\MainController::class)->group(function () {
    Route::get('/', 'main')->name('main');
    Route::post('data', 'data')->name('main.data');
});

// mypage
Route::controller(\App\Http\Controllers\Mypage\MypageController::class)->middleware('auth.check')->prefix('mypage')->group(function () {
    Route::get('/', 'index')->name('mypage');
    Route::post('data', 'data')->name('mypage.data');
});

// auth
Route::prefix('auth')->group(function () {
    Route::controller(\App\Http\Controllers\Auth\AuthController::class)->group(function () {
        Route::middleware('guest')->group(function () {
            Route::get('signup', 'signup')->name('auth.signup');
        });

        Route::post('data', 'data')->name('auth.data');
    });

    Route::controller(\App\Http\Controllers\Auth\LoginController::class)->group(function () {
        Route::match(['get', 'post'], 'login', 'login')->middleware('guest')->name('login');
        Route::post('logout', 'logout')->middleware('auth.check')->name('logout');
    });
});

require __DIR__ . '/common.php';
