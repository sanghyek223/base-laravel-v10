<?php

namespace App\Services;

/**
 * Class MailTemplateService
 * @package App\Services
 */
class MailTemplateServices extends AppServices
{
    // 회원가입
    public function signupCompleteMail($user, $send = true) // 데이터만 리턴 받고 싶을때 $send => false
    {
        $mailData = [
            'receiver_name' => $user->getNameEn(),
            'receiver_email' => $user->uid,
            'body' => view("template.mail-signup.complete", ['user' => $user])->render(),
        ];

        return ($send)
            ? (new MailRealSendServices())->mailSendService($mailData, 'signup-complete') // 발송
            : $mailData; // 데이터 리턴
    }

    // 비밀번호 찾기
    public function findPasswordMail($user, $temp_pw, $send = true) // 데이터만 리턴 받고 싶을때 $send => false
    {
        $mailData = [
            'receiver_name' => $user->getNameEn(),
            'receiver_email' => $user->uid,
            'body' => view("template.mail-find-password", ['user' => $user, 'temp_pw' => $temp_pw])->render(),
        ];

        return ($send)
            ? (new MailRealSendServices())->mailSendService($mailData, 'find-password') // 발송
            : $mailData; // 데이터 리턴
    }
}
