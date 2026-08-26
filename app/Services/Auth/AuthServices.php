<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Services\AppServices;
use App\Services\CommonServices;
use App\Services\MailTemplateServices;
use Illuminate\Http\Request;

/**
 * Class AuthServices
 * @package App\Services
 */
class AuthServices extends AppServices
{
    public function signupAction(Request $request)
    {
        return $this->data;
    }

    public function dataAction(Request $request)
    {
        switch ($request->case) {
            case 'uid-check':
                return $this->uidCheck($request);

            case 'license-check':
                return $this->licenseCheck($request);

            case 'find-id':
                return $this->findID($request);

            case 'find-pw':
                return $this->findPW($request);

            case 'user-create':
                return $this->userCreate($request);

            default:
                return notFoundRedirect();
        }
    }

    public function uidCheck(Request $request)
    {
        $uid = $request->uid;

        // ID (이메일) 유효성 체크
        $validator = validator()->make(['uid' => $uid], [
            'uid' => 'required|email',
        ]);

        if ($validator->fails()) {

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => errorMsg('email'),
                'focus' => '#uid',
                'input' => [
                    $this->ajaxActionInput('#uid', ''),
                ],
            ]);
        }

        if (User::where('uid', $uid)->exists()) {

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => "이미 사용중인 ID 입니다.\n새 ID를 입력해주세요.",
                'focus' => '#uid',
                'input' => [
                    $this->ajaxActionInput('#uid', ''),
                ],
            ]);
        }

        return $this->returnJsonData('alert', [
            'case' => true,
            'msg' => "사용 가능한 아이디 입니다.",
            'data' => [
                $this->ajaxActionData('#uid', 'check', 'Y'),
            ],
        ]);
    }

    public function licenseCheck(Request $request)
    {
        $u_sid = deCryptString($request->u_sid);

        $query = User::where('license_number', $request->license_number);

        if ($u_sid != 0 /* 회원 sid 있을경우 제외 후 중복 체크 */) {
            $query->where('sid', '!=', $u_sid);
        }

        if ($query->exists()) {

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => "이미 사용중인 면허번호입니다.\n새 면허번호를 입력해주세요..",
                'focus' => '#license_number',
                'input' => [
                    $this->ajaxActionInput('#license_number', ''),
                ],
            ]);
        }

        return $this->returnJsonData('alert', [
            'case' => true,
            'msg' => "사용 가능한 면허번호 입니다.",
            'data' => [
                $this->ajaxActionData('#license_number', 'check', 'Y'),
            ],
        ]);
    }

    private function findID(Request $request)
    {
        $name = trim($request->name);
        $mobile = trim($request->mobile);

        $query = User::where(['name_kr' => $name]);
        $user = $query->whereRaw("RIGHT(REPLACE(mobile, '-', ''), 4) = ?", [$mobile])->first();

        if (empty($user->sid)) {
            return $this->returnJsonData('alert', [
                'msg' => "일치하는 정보가 없습니다.",
            ]);
        }

        $resultView = view('auth.find.include.id-result', ['user' => $user])->render();

        return $this->returnJsonData('append', [
            $this->ajaxActionHtml('#find-id-frm .find-conbox', $resultView),
        ]);
    }

    private function findPW(Request $request)
    {
        $this->transaction();

        try {
            $uid = trim($request->uid);
            $user = User::where('uid', $uid)->first();

            if (empty($user->sid)) {
                return $this->returnJsonData('alert', [
                    'case' => true,
                    'msg' => 'The ID you entered is incorrect. Please try again',
                    'focus' => '#uid',
                ]);
            }

            // 임시비밀번호
            $n1 = random_int(10, 99);
            $n2 = random_int(10, 99);
            $temp_pw = $n2 . now()->format('is') . $n1;

            $user->passwordChange($temp_pw);
//            $user->imsi_password = 'Y';
            $user->update();

            // 비밀번호 찾기 메일 발송
            $mailResult = (new MailTemplateServices())->findPasswordMail($user, $temp_pw);

            if ($mailResult !== 'suc') {
                return $mailResult;
            }
            // END 비밀번호 찾기 메일 발송

            $this->dbCommit('임시비밀번호 메일 발송');
            $resultView = view('auth.find.include.pw-result', ['user' => $user, 'temp_pw' => $temp_pw])->render();

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => "Your Password has been sent to {$user->uid}",

                'input' => [
                    $this->ajaxActionInput('#uid', ''),
                ],

                'append' => [
                    $this->ajaxActionHtml('#find-pw-frm .find-conbox', $resultView),
                ],
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function userCreate(Request $request)
    {
        $uid = $request->uid;

        // ID (이메일) 유효성 체크
        $validator = validator()->make(['uid' => $uid], [
            'uid' => 'required|email',
        ]);

        if ($validator->fails()) {

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => errorMsg('email'),
                'focus' => '#uid',
                'input' => [
                    $this->ajaxActionInput('#uid', ''),
                ],
            ]);
        }

        // DB 저장전 한번더 ID 중복 체크
        if (User::where('uid', $uid)->exists()) {

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => "이미 사용중인 ID 입니다.\n새 ID를 입력해주세요.",
                'focus' => '#uid',
                'input' => [
                    $this->ajaxActionInput('#uid', ''),
                ],
            ]);
        }

        $captchaResult = (new CommonServices())->captchaCheckService($request->captcha);

        if ($captchaResult !== 'suc') {
            return $captchaResult;
        }

        $this->transaction();

        try {
            $user = new User();
            $user->setByData($request);
            $user->save();

            // 회원가입 메일 발송
            $mailResult = (new MailTemplateServices())->signupCompleteMail($user);

            if ($mailResult !== 'suc') {
                return $mailResult;
            }
            // END 회원가입 메일 발송

            $this->dbCommit('회원가입 완료');

            // 회원가입 완료 페이지 진입시 가입 세션 필요
            session()->put('signup_sid', $user->sid);

            $replaceUrl = route('auth.signup.complete');
            $ajaxLocation = $this->ajaxActionLocation('replace', $replaceUrl);

            return $this->returnJsonData('location', $ajaxLocation);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }
}
