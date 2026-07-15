<?php

namespace App\Services;

use App\Models\MailSend;
use App\Models\WiseUMailBody;
use App\Models\WiseUMailInterface;
use Illuminate\Support\Facades\DB;

/**
 * Class MailSendServices
 * @package App\Services
 */
class MailRealSendServices extends AppServices
{
    private $mailConfig;
    private $plannerMail = ''; // 기획자 메일
    private $secretariatMail = ''; // 학회사무국 메일

    public function __construct()
    {
        $this->mailConfig = getConfig('mail');
    }

    // 메일 수신 대상 추가
    private function mailSendTargetAppend($mailData)
    {
        return [
            [ // 기획자
                'receiver_name' => $mailData['receiver_name'],
                'receiver_email' => $this->plannerMail,
                'body' => $mailData['body'],
            ],

            [ // 사무국
                'receiver_name' => $mailData['receiver_name'],
                'receiver_email' => $this->secretariatMail,
                'body' => $mailData['body'],
            ],
        ];
    }

    public function mailSendService($mailData, $case, $additionalData = null)
    {
        switch ($case) {
            case 'signup':
                $subject = '';
                $data[] = $mailData;
//                $data = array_merge($data, $this->mailSendTargetAppend($mailData));
                break;

            case 'admin-type-send': // 관리자 메일 발송
                $subject = $additionalData['subject'];
                $sender = [
                    'sender_name' => $additionalData['sender_name'],
                    'sender_email' => $additionalData['sender_email'],
                ];

                $data = $mailData;

//                foreach ($mailData as $key => $val) {
//                    $data = array_merge($data, $this->mailSendTargetAppend($val));
//                }
                break;

            default:
                return notFoundRedirect();
        }

        return $this->mailSend($data, $subject, $sender ?? null);
    }

    // 메일 발송 로직
    private function mailSend($mailData, $subject, $sender = null)
    {
        $mailConfig = $this->mailConfig;

        DB::beginTransaction();

        try {

            foreach ($mailData as $key => $data) {
                $now = now();
                $seq = $now->timestamp . $now->micro;

                $body = $data['body'];

                $receiver_name = $data['receiver_name'];
                $receiver_email = $data['receiver_email'];

                // 특정 ip 예외처리
                switch ($_SERVER['REMOTE_ADDR']) {
                    case '218.235.94.217':
                        $receiver_email = 'sh.han@m2community.co.kr';
                        break;
                }

//                /* 와이즈유 모델로 호출 안될때 강제 커넥션 */
//                $wiseUconnection = wiseuConnection();

//                //인터페이스 테이블
//                $stmt = $wiseUconnection->prepare("INSERT INTO NVREALTIMEACCEPT
//                    (ECARE_NO, RECEIVER_ID, CHANNEL, SEQ, REQ_DT, REQ_TM, TMPL_TYPE, RECEIVER_NM, RECEIVER, SENDER_NM, SENDER, SUBJECT, SEND_FG, DATA_CNT)
//                    VALUES (:ECARE_NO, :RECEIVER_ID, :CHANNEL, :SEQ, :REQ_DT, :REQ_TM, :TMPL_TYPE, :RECEIVER_NM, :RECEIVER, :SENDER_NM, :SENDER, :SUBJECT, :SEND_FG, :DATA_CNT)");
//
//                $stmt->execute([
//                    ':ECARE_NO' => $mailConfig['eCareNo'],
//                    ':RECEIVER_ID' => $seq,
//                    ':CHANNEL' => 'M',
//                    ':SEQ' => $seq,
//                    ':REQ_DT' => $now->format('Ymd'),
//                    ':REQ_TM' => $now->format('His'),
//                    ':TMPL_TYPE' => 'T',
//                    ':RECEIVER_NM' => $receiver_name,
//                    ':RECEIVER' => $receiver_email,
//                    ':SENDER_NM' => $sender['sender_name'] ?? $mailConfig['sender_name'],
//                    ':SENDER' => $sender['sender_email'] ?? $mailConfig['sender_email'],
//                    ':SUBJECT' => $subject,
//                    ':SEND_FG' => 'R',
//                    ':DATA_CNT' => 1,
//                ]);
//                /* END 와이즈유 모델로 호출 안될때 강제 커넥션 */
//
//                //메일 body
//                $stmt = $wiseUconnection->prepare("INSERT INTO NVREALTIMEACCEPTDATA (SEQ, DATA_SEQ, ATTACH_YN, DATA) VALUES (:SEQ, :DATA_SEQ, :ATTACH_YN, :DATA)");
//
//                $stmt->execute([
//                    ':SEQ' => $seq,
//                    ':DATA_SEQ' => 1,
//                    ':ATTACH_YN' => 'N',
//                    ':DATA' => $body,
//                ]);

                //인터페이스 테이블
                WiseUMailInterface::insert([
                    'ECARE_NO' => $mailConfig['eCareNo'],
                    'RECEIVER_ID' => $seq,
                    'CHANNEL' => 'M',
                    'SEQ' => $seq,
                    'REQ_DT' => $now->format('Ymd'),
                    'REQ_TM' => $now->format('His'),
                    'TMPL_TYPE' => 'T',
                    'RECEIVER_NM' => $receiver_name,
                    'RECEIVER' => $receiver_email,
                    'SENDER_NM' => $sender['sender_name'] ?? $mailConfig['sender_name'],
                    'SENDER' => $sender['sender_email'] ?? $mailConfig['sender_email'],
                    'SUBJECT' => $subject,
                    'SEND_FG' => 'R',
                    'DATA_CNT' => 1,
                ]);

                //메일 body
                WiseUMailBody::insert([
                    'SEQ' => $seq,
                    'DATA_SEQ' => 1,
                    'ATTACH_YN' => 'N',
                    'DATA' => $body,
                ]);

                // 메일 발송내역 저장
                MailSend::insert([
                    'ml_sid' => $data['ml_sid'] ?? 0,
                    'wiseu_seq' => $seq,
                    'receiver_name' => $receiver_name,
                    'receiver_email' => $receiver_email,
                    'subject' => $subject,
                    'contents' => $body,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return 'suc';
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }
}
