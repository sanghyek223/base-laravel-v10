<?php

return [
    // ================= web menu =================
    'main' => [
        'M1' => [
            'name' => 'Home',
            'route' => 'main',
            'param' => [],
            'url' => null,
            'blank' => false,
            'dev' => false,
            'continue' => false,
        ],

        'MYPAGE' => [
            'name' => '마이페이지',
            'route' => 'mypage',
            'param' => [],
            'url' => null,
            'blank' => false,
            'dev' => false,
            'continue' => true,
        ],

        'GUEST' => [
            'name' => 'Sign-up',
            'route' => null,
            'param' => [],
            'url' => "javascript::alert('준비중');",
            'blank' => false,
            'dev' => false,
            'continue' => true,
        ],
    ],

    'sub' => [
        'M1' => [ // Home
            'S1' => [
                'name' => 'Home',
                'route' => null,
                'param' => [],
                'url' => "javascript::alert('준비중');",
                'blank' => false,
                'dev' => false,
                'continue' => false,
            ],
        ],

        'GUEST' => [ // GUEST
            'S1' => [
                'name' => '로그인',
                'route' => 'login',
                'param' => [],
                'url' => null,
                'blank' => false,
                'dev' => false,
                'continue' => false,
            ],
        ],

        'MYPAGE' => [ // 마이페이지
            'S1' => [
                'name' => '개인정보수정',
                'route' => null,
                'param' => [],
                'url' => "javascript::alert('준비중');",
                'blank' => false,
                'dev' => false,
                'continue' => false,
            ],
        ],
    ],
];
