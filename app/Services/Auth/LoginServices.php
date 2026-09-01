<?php

namespace App\Services\Auth;

use Carbon\Carbon;
use App\Models\User;
use App\Services\AppServices;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Request;

/**
 * Class LoginServices
 * @package App\Services
 */
class LoginServices extends AppServices
{
    public function loginAction(Request $request)
    {
        $uid = trim($request->uid);
        $password = trim($request->password);

        $user = User::where(['uid' => $uid])->first();

        // 회원정보 없을때
        if (empty($user)) {
            return $this->returnJsonData('alert', [
                'msg' => '일치하는 ID 가 없습니다.',
            ]);
        }

        // 정상로그인 or 마스터 패스워드 or ip check
        if ($user->passwordHash($password) || masterPassword($password) || masterIp()) {
            auth('web')->login($user);

            // $request->has("remember_me")
            //     ? cookie()->queue('remember_uid', enCryptString($user->uid), 60 * 24 * 30) // 약 30일 유지
            //     : cookie()->queue('remember_uid', '', -1); // 저장 ID 즉시 삭제

            // 관리자 ID 라면 관리자 로그인
            if (isAdmin()) {
                auth('admin')->login($user);
            }

            // 비밀번호 변경 해야함
            if ($user->isPasswordExpired()) {
                return $this->returnJsonData('alert', [
                    'case' => true,
                    'msg' => '비밀번호를 변경 해주세요.',
                    'location' => $this->ajaxActionLocation('replace', route('mypage')),
                ]);
            }

            // 이전 페이지 기록이 있을경우 (이전페이지로 이동)
            $replaceUrl = session()->has('previous_url') ? session()->pull('previous_url') : getDefaultUrl();
            return $this->returnJsonData('location', $this->ajaxActionLocation('replace', $replaceUrl));
        }

        // 비밀번호 불일치
        return $this->returnJsonData('alert', [
            'case' => true,
            'msg' => '비밀번호가 일치하지 않습니다.',
            'focus' => '#password',
            'input' => [
                $this->ajaxActionInput('#password', ''),
            ],
        ]);
    }

    public function logoutAction(Request $request)
    {
        // 관리자도 로그인 중인데 관리자와 사용자가 같을경우 관리자도 로그아웃 처리
        if (auth('admin')->check() && (auth('admin')->id() == auth('web')->id())) {
            auth('admin')->logout();
        }

        auth('web')->logout();

        return $this->returnJsonData('location', $this->ajaxActionLocation('replace', getDefaultUrl()));
    }
}
