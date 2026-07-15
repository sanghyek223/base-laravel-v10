<?php

return [
    // ================= admin menu =================
    'main' => [
        'M1' => [
            'name' => '회원 관리',
            'route' => null,
            'param' => [],
            'url' => 'javascript:void(0);',
            'blank' => false,
            'dev' => false,
            'continue' => false,
        ],

        'mail' => [
            'name' => '메일 발송',
            'route' => null,
            'param' => [],
            'url' => 'javascript:void(0);',
            'blank' => false,
            'dev' => false,
            'continue' => false,
        ],

        'stat' => [
            'name' => '로그 관리',
            'route' => null,
            'param' => [],
            'url' => 'javascript:void(0);',
            'blank' => false,
            'dev' => true,
            'continue' => true,
        ],
    ],

    'sub' => [
        'M1' => [
            'S1' => [
                'name' => '전체 회원',
                'route' => 'member',
                'param' => [],
                'url' => null,
                'blank' => false,
                'dev' => false,
                'continue' => false,
            ],

            'S2' => [
                'name' => '탈퇴 회원',
                'route' => 'member',
                'param' => ['case' => 'withdrawal'],
                'url' => null,
                'blank' => false,
                'dev' => false,
                'continue' => false,
            ],
        ],

        'mail' => [
            'S1' => [
                'name' => '메일 관리',
                'route' => 'mail',
                'param' => [],
                'url' => null,
                'blank' => false,
                'dev' => false,
                'continue' => false,
            ],

            'S2' => [
                'name' => '주소록 관리',
                'route' => 'mail.address',
                'param' => [],
                'url' => null,
                'blank' => false,
                'dev' => false,
                'continue' => false,
            ],
        ],

        'stat' => [
            'S1' => [
                'name' => '접속 통계',
                'route' => 'stat',
                'param' => [],
                'url' => null,
                'blank' => false,
                'dev' => false,
                'continue' => false,
            ],

            'S2' => [
                'name' => '접속 경로',
                'route' => 'stat.referer',
                'param' => [],
                'url' => null,
                'blank' => false,
                'dev' => false,
                'continue' => false,
            ],
        ],
    ]
];
