<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Services\AppServices;
use App\Services\MailRealSendServices;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
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

    private function userCreate(Request $request)
    {
        $this->transaction();

        try {
            $user = new User();
            $user->setByData($request);
            $user->save();

            // 회원가입 메일 발송
            $mailData = [
                'receiver_name' => $user->name_kr,
                'receiver_email' => $user->uid,
                'body' => view("sponsor.template.mail-signup", ['user' => $user])->render(),
            ];

            $mailResult = (new MailRealSendServices())->mailSendService($mailData, 'signup');

            if ($mailResult !== 'suc') {
                return $mailResult;
            }
            // END 회원가입 메일 발송

            $this->dbCommit('회원가입 완료');

            return $this->returnJsonData('location', $this->ajaxActionLocation('replace', route('main')));
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }
}
