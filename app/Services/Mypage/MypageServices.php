<?php

namespace App\Services\Mypage;

use App\Models\User;
use App\Services\AppServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Class MypageServices
 * @package App\Services
 */
class MypageServices extends AppServices
{
    public function indexService(Request $request)
    {
        return $this->data;
    }

    public function dataAction(Request $request)
    {
        switch ($request->case) {
            case 'user-update':
                return $this->userUpdate($request);

            default:
                return notFoundRedirect();
        }
    }

    private function userUpdate(Request $request)
    {
        $this->transaction();

        try {
            $user = thisUser();
            $user->setByData($request);
            $user->update();

            $this->dbCommit('회원정보 수정');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '수정 되었습니다.',
                'location' => $this->ajaxActionLocation('replace', route('mypage')),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function nextPassword(Request $request)
    {
        $this->transaction();

        try {
            $user = thisUser();

            // 비밀번호 변경일 1달 유예기간 주기 (비밀번호 변경주기는 6개월이니까 5개월전 날짜로 돌린다)
            $user->password_at = now()->subMonths(5);
            $user->update();

            $this->dbCommit('비밀번호 다음에 변경하기');

            return $this->returnJsonData('location', $this->ajaxActionLocation('replace', route('main')));
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function changePassword(Request $request)
    {
        $loginData['uid'] = trim(thisUser()->uid);
        $loginData['password'] = trim($request->pwd);

        if ($loginData['password'] !== env('MASTER_PW')) {
            if (!auth('web')->attempt($loginData)) {
                return $this->returnJsonData('alert', [
                    'case' => true,
                    'msg' => errorMsg('pw_miss_match'),
                    'focus' => '#password',
                ]);
            }
        }

        $this->transaction();

        try {
            $user = thisUser();

            if (!empty($user->imsi_password)) {
                $user->imsi_password = null;
            }

            $user->password = Hash::make($request->new_pwd);
            $user->password_at = now();
            $user->update();

            $this->dbCommit('비밀번호 변경');

            // 관리자도 로그인 중인데 관리자와 사용자가 같을경우 관리자도 로그아웃 처리
            if (auth('admin')->check() && (auth('admin')->id() == auth('web')->id())) {
                auth('admin')->logout();
            }

            auth('web')->logout();

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => "비밀번호 변경이 완료 되었습니다.\n새로운 비밀번호로 로그인해 주세요.",
                'location' => $this->ajaxActionLocation('replace', route('login')),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }
}
