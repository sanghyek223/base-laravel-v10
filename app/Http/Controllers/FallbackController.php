<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FallbackController extends Controller
{
    public function handle(Request $request)
    {
        // 요청된 URL에서 확장자 가져오기
        $extension = pathinfo($request->path(), PATHINFO_EXTENSION);

        // 정적 파일 요청이라면 404 반환 (예외 처리)
        if ($this->extensionCheck($extension)) {
            return response()->view('errors.404', [], 404);
        }

        // 리다이렉션 처리 함수 호출
        return notFoundRedirect();
    }

    private function extensionCheck($extension)
    {
        // 정적 파일 확장자 목록
        $staticExtensions = [
            'jpg',
            'jpeg',
            'png',
            'gif',
            'css',
            'js',
            'svg',
            'woff',
            'woff2',
            'ttf',
            'eot',
            'otf',
            'mp4',
            'webm',
            'ogg',
            'mp3',
            'wav',
            'ico'
        ];

        return in_array(strtolower($extension), $staticExtensions);
    }
}
