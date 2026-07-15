<?php

namespace App\Services\Admin\Auth;

use App\Services\AppServices;
use Illuminate\Http\Request;

/**
 * Class LoginServices
 * @package App\Services
 */
class LoginServices extends AppServices
{
    public function logoutAction(Request $request)
    {
        // 관리자 페이지 로그아웃은 사용자페이지도 로그아웃
        auth('admin')->logout();
        auth('web')->logout();

        return $this->returnJsonData('location', $this->ajaxActionLocation('replace', env('APP_URL')));
    }
}
