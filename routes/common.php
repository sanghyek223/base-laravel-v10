<?php

/*
|--------------------------------------------------------------------------
| Common Routes
|--------------------------------------------------------------------------
*/
Route::controller(\App\Http\Controllers\Controller::class)->prefix('common')->group(function () {
    /*
     * File Download URL
     * type => only: 단일, zip: 일괄다운(zip)
     * case => switch 문 구분값
     * sid => sid 값 enCryptString(sid) 로 암호화해서 전송
     */
    Route::get('download/{type}/{case}/{sid}', 'download')->where('type', 'only|zip')->name("download");

    Route::post('captcha-make', 'captchaMake')->name("captcha.make");
    Route::post('tinyUpload', 'tinyUpload')->name("tinyUpload");
    Route::post('plUpload', 'plUpload')->name("plUpload");
});

/*
|--------------------------------------------------------------------------
| Fallback Routes
|--------------------------------------------------------------------------
*/
//Route::fallback([\App\Http\Controllers\FallbackController::class, 'handle']);
