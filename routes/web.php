<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// main
Route::controller(\App\Http\Controllers\Main\MainController::class)->group(function () {
    Route::get('/', 'index')->name('main');
    Route::post('data', 'data')->name('main.data');
});

// Mypage
Route::controller(\App\Http\Controllers\Mypage\MypageController::class)->middleware('auth.check')->prefix('mypage')->group(function () {
    Route::get('/', 'index')->name('mypage');
    Route::post('data', 'data')->name('mypage.data');
});

// 게시판
Route::controller(\App\Http\Controllers\Board\BoardController::class)->middleware('board.check')->prefix('board/{code}')->group(function () {
    Route::get('/', 'index')->name('board');
    Route::get('view/{sid}', 'view')->name('board.view');
    Route::get('upsert/{sid?}', 'upsert')->name('board.upsert');
    Route::post('data', 'data')->name('board.data');

    Route::controller(\App\Http\Controllers\Board\ReplyController::class)->prefix('reply')->group(function () {
        Route::get('{b_sid}/view/{sid}', 'view')->name('board.reply.view');
        Route::get('{b_sid}/upsert/{sid?}', 'upsert')->name('board.reply.upsert');
        Route::post('data', 'data')->name('board.reply.data');
    });
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
