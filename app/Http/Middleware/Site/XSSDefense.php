<?php

namespace App\Http\Middleware\Site;

use Closure;
use Illuminate\Http\Request;
use Mews\Purifier\Facades\Purifier;

class XSSDefense
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // 사용자 입력 필터링
        $input = $request->all();
        $this->sanitizeInput($input);
        $request->replace($input);

        return $next($request);
    }

    private function sanitizeInput(array &$input)
    {
        foreach ($input as $key => &$value) {
            switch (gettype($value)) {
                case 'array':
                    // 배열인 경우 재귀적으로 호출
                    $this->sanitizeInput($value);
                    break;

                case 'string':
//                    // 문자열인 경우에만 HTMLPurifier를 사용하여 안전한 HTML로 변환
//                    $value = Purifier::clean($value);
//
//                    $value = htmlspecialchars_decode($value);

                    // UTF-8 인코딩에서 비분리 공백 찾기 (줄바꿈 ? 로 변경되는 이슈있어서 추가)
                    $value =  preg_replace('/\x{00A0}/u', '&nbsp;', $value);

                    // 다시 필터링 - 스크립트 태그 제거 (대소문자 무관)
                    $value = preg_replace('/<\s*script\b[^>]*>(.*?)<\s*\/\s*script\s*>/is', '', $value);

                    // 숨겨진 스크립트 감지 (HTML 이스케이프된 태그)
                    $value = preg_replace('/<\s*\\\\s*c\s*r\s*i\s*p\s*t\b[^>]*>(.*?)<\s*\/\s*\\\\s*c\s*r\s*i\s*p\s*t\s*>/is', '', $value);

                    // 이벤트 핸들러 제거 (onclick, onload 등)
                    $value = preg_replace('/\s+on\w+\s*=\s*(["\']?).*?\1/i', '', $value);

                    // javascript: URL 제거
                    $value = preg_replace('/javascript\s*:/i', 'removed:', $value);

                    // data: URL에서 위험한 mime-type 제거
                    $value = preg_replace('/data:text\/html/i', 'data:removed/html', $value);
                    $value = preg_replace('/data:application\/javascript/i', 'data:removed/javascript', $value);
                    break;

                default:
                    break;
            }
        }
    }
}
