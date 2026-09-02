<?php

return [
    // ================== asset version ==================
    'asset_version' => '20250508',

    // ================== master ip (password 체크 무시) ==================
    'masterIP' => [
        '218.235.94.217',
    ],

    // ================== debug ip (debugbar 노출) ==================
    'debugIP' => [
        '218.235.94.217',
    ],

    // ================== dev ip (error 페이지 노출) ==================
    'devIP' => [
        '218.235.94.217',
    ],

    // ================= api =================
    'api' => [
        'url' => env('APP_URL') . '/api',
    ],

    // ================= web =================
    'web' => [
        'app_name' => env('APP_NAME'),
        'url' => env('APP_URL'),
    ],

    // ================= admin =================
    'admin' => [
        'app_name' => '관리자 | ' . env('APP_NAME'),
        'url' => env('APP_URL') . '/admin',
    ],

    'info' => [
        'address' => '',
        'email' => '',
        'tel' => '',
        'fax' => '',

        'bank' => '',
        'account' => '',
        'account_holder' => '',
    ],
];
