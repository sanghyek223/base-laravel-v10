<?php

require_once('SiteHelper.php');

// set Flash session
if (!function_exists('setFlashData')) {
    function setFlashData(array $data): void
    {
        foreach ($data as $key => $val) {
            session()->flash($key, $val);
        }
    }
}

// error msg
if (!function_exists('errorMsg')) {
    function errorMsg(string $code): string
    {
        return config("site.error.{$code}") ?? $code;
    }
}

// DB ERROR
if (!function_exists('dbRedirect')) {
    function dbRedirect()
    {
        return errorRedirect('reload', 'db');
    }
}

// 접근권한 없음
if (!function_exists('denyRedirect')) {
    function denyRedirect()
    {
        return errorRedirect('back', 'deny');
    }
}

// 로그인 필요
if (!function_exists('authRedirect')) {
    function authRedirect()
    {
        // ajax 요청이 아닐때
        if (!request()->ajax()) {
            // 현재 URL을 세션에 저장
            session(['previous_url' => request()->fullUrl()]);
        }

        return errorRedirect('replace', 'auth');
    }
}

// SERVER ERROR
if (!function_exists('serverRedirect')) {
    function serverRedirect()
    {
        return errorRedirect('replace', '500');
    }
}

// 404 not found
if (!function_exists('notFoundRedirect')) {
    function notFoundRedirect()
    {
        return errorRedirect('replace', '404');
    }
}

// 419 CSRF Token Expires
if (!function_exists('CSRFRedirect')) {
    function CSRFRedirect()
    {
        return errorRedirect('reload', '419');
    }
}

// custom error redirect
if (!function_exists('errorRedirect')) {
    function errorRedirect(string $redirect, string $code, $url = null)
    {
        /*
         * $redirect 종류
         * back, reload, replace
         * back => 뒤로
         * reload => 새로고침
         * replace => 페이지 이동, (url 변수 필요 없을경우 메인페이지)
         *
         * $code
         * config 에 설정된 site.error 키값이 없을경우 변수값으로 에러메세지 출력
         */

        $referer = request()->headers->get('referer');

        $json = [
            'msg' => errorMsg($code),
            'url' => $url ?? getDefaultUrl($code == 'auth'),
            'redirect' => $redirect,
        ];

        if (request()->ajax()) {
            unset($json['url']);
            unset($json['redirect']);

            $json['ajax'] = true;
        } else {
            // 뒤로가인데 뒤로갈 페이지 없을경우
            if ($redirect === 'back' && empty($referer)) {
                $json['redirect'] = 'replace';
                $json['url'] = $url ?? getDefaultUrl($code == 'auth');
            }
        }

        setFlashData($json);
        abort(543); // 커스텀 에러 발생 시키기;
    }
}

// 커스텀 리다이렉트 처리 함수
if (!function_exists('handleCustomRedirect')) {
    function handleCustomRedirect()
    {
        $redirectType = session()->pull('redirect');
        $message = session()->pull('msg');
        $url = session()->pull('url');

        if (session()->pull('ajax')) {
            return response()->json([
                'reload' => true,
                'status' => 'fail',
                'msg' => '허용되지 않은 접근입니다.',
            ]);
        }

        switch ($redirectType) {
            case 'back':
                return redirect()->back()->with(['msg' => $message]);

            case 'reload':
                return redirect()->refresh()->with(['msg' => $message]);

            case 'replace':
                return redirect($url)->with(['msg' => $message]);

            default:
                return redirect(getDefaultUrl());
        }
    }
}

// set seq (paging 없을때)
if (!function_exists('setSeq')) {
    function setSeq(object $data, $sortName = null)
    {
        $count = 0;
        $total = count($data);
        $sortName = ($sortName ?? 'seq');

        // seq 라는 순번 필드를 추가
        foreach ($data as $key => $row) {
            $data[$key]->{$sortName} = $total - $count;
            $count++;
        }

        return $data;
    }
}

// set list seq (paging 있을때)
if (!function_exists('setListSeq')) {
    function setListSeq(object $data, $sortName = null)
    {
        $count = 0;
        $total = $data->total();
        $perPage = $data->perPage();
        $currentPage = $data->currentPage();
        $sortName = ($sortName ?? 'seq');

        // seq 라는 순번 필드를 추가
        $data->getCollection()->transform(function ($data) use ($total, $perPage, $currentPage, $sortName, &$count) {
            $data->{$sortName} = ($total - ($perPage * ($currentPage - 1))) - $count;
            $count++;
            return $data;
        });

        return $data;
    }
}

// set array seq (array 형태일때)
if (!function_exists('setArraySeq')) {
    function setArraySeq(array $data, $sortName = null)
    {
        $count = 0;
        $total = count($data);
        $sortName = ($sortName ?? 'seq');

        // seq 라는 순번 필드를 추가
        foreach ($data as $key => $row) {
            $data[$key][$sortName] = $total - $count;
            $count++;
        }

        return $data;
    }
}

// Crypt::encryptString 적용
if (!function_exists('enCryptString')) {
    function enCryptString($string): string
    {
        return \Illuminate\Support\Facades\Crypt::encryptString($string);
    }
}

// Crypt::decryptString 적용
if (!function_exists('deCryptString')) {
    function deCryptString($string): string
    {
        return \Illuminate\Support\Facades\Crypt::decryptString($string);
    }
}

// 숫자 앞에 0 붙이기
if (!function_exists('addZero')) {
    function addZero(int $num, int $len)
    {
        return sprintf("%0{$len}d", $num);
    }
}

// 숫자 콤마 제거
if (!function_exists('unComma')) {
    function unComma(string $num)
    {
        return (int)str_replace(',', '', $num);
    }
}

// jsonUnicode 적용
if (!function_exists('jsonUnicode')) {
    function jsonUnicode($aray = [])
    {
        return json_encode($aray, JSON_UNESCAPED_UNICODE);
    }
}

// search encoding
if (!function_exists('searchEncoding')) {
    function searchEncoding($string)
    {
        $encode = ['ASCII', 'UTF-8', 'EUC-KR', 'CP949', 'ISO-8859-1', 'SJIS', 'GB2312'];
        $str_encode = mb_detect_encoding($string, $encode); // 문자셋을 감지 못하면 false

        return $str_encode ?: 'Unknown';
    }
}

// convert utf8
if (!function_exists('convertUTF8')) {
    function convertUTF8($string)
    {
        $encoding = searchEncoding($string);

        switch ($encoding) {
            case 'UTF-8':
                return $string;

            case 'Unknown':
                return '변환할수 없습니다.';

            default:
                return iconv($encoding, 'UTF-8', $encoding);
        }
    }
}

// device check
if (!function_exists('isDevice')) {
    function isDevice()
    {
        $agent = new \Jenssegers\Agent\Agent;

        if ($agent->isDesktop()) {
            return "P";
        }

        if ($agent->isTablet()) {
            return "T";
        }

        return "M";
    }
}

// mobile check
if (!function_exists('isMobile')) {
    function isMobile(): bool
    {
        $agent = new \Jenssegers\Agent\Agent;
        return ($agent->isMobile() || $agent->isTablet());
    }
}

// browser
if (!function_exists('isBrowser')) {
    function isBrowser()
    {
        $agent = new \Jenssegers\Agent\Agent;
        return $agent->browser();
    }
}

// 마스터 비밀번호
if (!function_exists('masterPW')) {
    function masterPW($password): bool
    {
        $masterPW = [
            env('MASTER_PW'),
        ];

        return in_array($password, $masterPW);
    }
}

// 마스터 IP (비밀번호 체크 무시등)
if (!function_exists('masterIP')) {
    function masterIP(): bool
    {
        return in_array(request()->ip(), config('site.app.masterIP'));
    }
}

// 디버그바 노출
if (!function_exists('isDebug')) {
    function isDebug(): bool
    {
        $ip = request()->ip();
        return (in_array($ip, config('site.app.debugIP')) || strpos($ip, '218.235.94.') !== false);
    }
}

// 개발자 체크
if (!function_exists('isDev')) {
    function isDev(): bool
    {
        $ip = request()->ip();
        return in_array($ip, config('site.app.devIP'));
    }
}

// 날짜 변환
if (!function_exists('carbonParse')) {
    function carbonParse($date, $format = 'F j, Y')
    {
        return \Carbon\Carbon::parse($date)->format($format);
    }
}

// 날짜별 요일 YYYY-MM-DD
if (!function_exists('getYoil')) {
    function getYoil(string $date)
    {
        $yoil = ['일', '월', '화', '수', '목', '금', '토'];
        return $yoil[date('w', strtotime($date))];
    }
}

// D-day
if (!function_exists('DDay')) {
    function DDay($date) // Y-m-d 형태로
    {
        $today = now()->startOfDay();
        $target = \Carbon\Carbon::parse($date)->startOfDay();  // 대상 날짜 Carbon 변환

        $diff = $today->diffInDays($target, false); // 날짜 차이 계산 (음수 허용)

        return match (true) {
            $diff < 0 => 'END', // 종료
            $diff === 0 => 'Today', // 당일
            default => "D-{$diff}", // 남은 날짜
        };
    }
}

// 금액 한글 표기
if (!function_exists('priceKo')) {
    function priceKo($price = 0)
    {
        // 콤마 제거 및 정수 변환
        $price = (int)unComma($price);

        // 0 미만은 빈 값 반환
        if ($price < 0) {
            return '';
        }

        // 0원은 무료로 표시
        if ($price === 0) {
            return '무료';
        }

        $numbers = ['', '일', '이', '삼', '사', '오', '육', '칠', '팔', '구']; // 숫자 한글 변환
        $units = ['', '십', '백', '천', '만', '십', '백', '천', '억']; // 자릿수 단위

        $result = '';
        $unitIndex = 0;

        // 가장 낮은 자릿수부터 순차적으로 변환
        while ($price > 0) {
            $number = $price % 10;

            if ($number > 0) {
                // 십, 백, 천, 만, 억 앞의 '일'은 생략
                $numberText = ($number === 1 && $unitIndex > 0)
                    ? ''
                    : $numbers[$number];

                // 숫자 + 단위를 결과 앞에 추가
                $result = $numberText . $units[$unitIndex] . $result;
            }

            // 다음 자릿수로 이동
            $price = (int)($price / 10);
            $unitIndex++;
        }

        return $result;
    }
}
