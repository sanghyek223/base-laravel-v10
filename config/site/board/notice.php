<?php

$code = 'notice';

return [
    'code' => $code, // 게시판 코드

    'skin' => $code, // 게시판 스킨

    'name' => '공지사항', // 게시판 명

    'subject' => '제목', // 게시판 Subject 명

    'viewPath' => "board", // 게시판 View 폴더 변경하고싶으면 변경 EX) eng.board

    'directory' => "/board/{$code}", // 게시판 업로드 경로

    'popup_style' => 'rolling', // rolling, layer

    'paginate' => 15, // 페이지별 노출 리스트 수

    'comment_paginate' => 20, // 댓글 노출 리스트 수

    'options' => [
        'hide' => ['N' => '공개', 'Y' => '비공개'], // 노출여부
        'main' => ['Y' => '노출', 'N' => '미노출'], // 메인노출
        'notice' => ['Y' => '사용', 'N' => '미사용'], // 상단공지
        'secret' => ['Y' => '사용', 'N' => '미사용'], // 비밀글
        'date_type' => ['D' => '하루', 'L' => '장기'], // 기간 타입

        'popup_yn' => ['N' => '미사용', 'Y' => '사용'], // 팝업 사용여부
        'popup_skin' => ['1' => '템플릿1', '2' => '템플릿2', '3' => '템플릿3', '4' => '템플릿4'], // 팝업 스킨
        'popup_detail' => ['N' => '설정안함', 'Y' => '설정함'], // 팝업 상세보기 링크 사용여부
        'popup_contents' => ['1' => '공지 내용과 동일', '2' => '팝업 내용 새로 작성'], // 팝업내용
    ],

    'permission' => [ // 권한 빈값은 전체 접근, 값이있을경우 해당 level 만 접근가능
        'list' => [], // 리스트 권한
        'view' => [], // 상세보기 권한
        'write' => ['M'], // 글쓰기 권한
    ],

    'use' => [ // 사용 유무
        'login' => false, // 로그인 필요
        'main' => true, // 메인노출
        'link' => true, // 상세링크
        'hide' => true, // 공개옵션
        'writer' => true, // 작성자
        'email' => true, // 이메일
        'date' => false, // 기간설정
        'popup' => true, // 팝업
        'place' => false, // 장소
        'notice' => true, // 공지
        'secret' => false, // 비밀글
        'subject' => true, // 제목
        'contents' => true, // 내용
        'gubun' => false, // 구분
        'category' => false, // 카테고리
        'reply' => false, // 답글
        'comment' => false, // 댓글
        'file' => false, // 파일업로드 (단일파일)
        'thumbnail' => false, // 썸네일파일
        'plupload' => true, // 파일업로드 (plupload) 사용
    ],

    'gubun' => [
        'name' => '구분', // 구분 명칭
        'type' => 'select', // radio or select
        'item' => [ // 게시판 카테고리 ex) key => value
            '1' => 'g1',
            '2' => 'g2',
        ],
    ],

    'category' => [
        'name' => '카테고리', // 카테고리 명칭
        'type' => 'radio', // radio or select
        'item' => [ // 게시판 카테고리 ex) key => value
            '1' => 'c1',
            '2' => 'c2',
        ],
    ],

    'file' => [ // 기본 max 5개 까지 업로드가능 그이상은 DB 추가 필요
        1 => [
            'name' => '파일1',
        ],

//        2 => [
//            'name' => '파일2',
//        ],
//
//        3 => [
//            'name' => '파일3',
//        ],
//
//        4 => [
//            'name' => '파일4',
//        ],
//
//        5 => [
//            'name' => '파일5',
//        ],
    ],

    'thumbnail' => [
        'name' => '썸네일', // 썸네일 명칭
    ],

    'date' => [
        'name' => '행사일정' // 일정 사용시 일정명
    ],

    'search' => [ // 검색 정보
        'subject/contents' => '제목 + 내용',
        'writer' => '작성자',
    ],

    'key' => [ // 메뉴 키
        'main' => 'M3',
        'sub' => 'S1'
    ],
];
