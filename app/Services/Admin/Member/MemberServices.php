<?php

namespace App\Services\Admin\Member;

use App\Models\User;
use App\Exports\MemberExcel;
use App\Services\AppServices;
use App\Services\CommonServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Class MemberServices
 * @package App\Services
 */
class MemberServices extends AppServices
{
    public function indexService(Request $request)
    {
        $memberCase = $request->case;

        $search_key = $request->search_key;
        $keyword = $request->keyword;

        $query = User::orderByDesc('sid');

        switch ($memberCase) {
            case 'withdrawal': // 탈퇴 회원
                $fileName = '탈퇴 회원';
                $query->whereNotNull('withdrawal_at');
                break;

            default:
                $fileName = '전체 회원';
                $query->whereNull('withdrawal_at');
                break;
        }

        if ($keyword) {
            switch ($search_key) {
                case 'mobile':
                    $query->where(function ($q) use ($keyword) {
                        $q->where('mobile', 'like', "%{$keyword}%")
                            ->orWhereRaw("REPLACE(mobile, '-', '') LIKE ?", ["%" . str_replace('-', '', $keyword) . "%"]);
                    });
                    break;

                default:
                    $query->where($search_key, 'like', "%{$keyword}%");
                    break;
            }
        }

        // 엑셀 다운로드 할때
        if ($request->excel) {
            $this->data['query'] = $query;
            $this->data['total'] = (clone $query)->count();

            $export = new MemberExcel($this->data);

            if (isDev()) {
                return view('admin.components.excel-preview', [
                        'previewData' => $export->getPreviewData(),
                        'fileName' => $fileName,
                    ]
                );
            }

            return (new CommonServices())->excelDownload($export, $fileName);
        }

        $list = $query->paginate(20)->appends($request->query());

        $this->data['list'] = setListSeq($list);
        $this->data['memberCase'] = empty($memberCase) ? [] : ['case' => $memberCase];

        return $this->data;
    }

    public function upsertService(Request $request)
    {
        $this->data['user'] = User::findOrFail($request->sid);

        return $this->data;
    }

    public function dataAction(Request $request)
    {
        switch ($request->case) {
            case 'user-update':
                return $this->userUpdate($request);

            case 'user-delete':
                return $this->userDelete($request);

            case 'user-login':
                return $this->userLogin($request);

            case 'pw-reset':
                return $this->passwordReset($request);

            case 'db-change':
                return $this->dbChange($request);

            default:
                return notFoundRedirect();
        }
    }

    private function userTimestampSet($user) // 회원 정보 수정시 업데이트 시간 자동 적용안함 해제
    {
        $user->timestamps = true;
    }

    private function userUpdate(Request $request)
    {
        $this->transaction();

        try {
            $user = User::findOrFail($request->sid);
            $user->setByData($request);
            $user->update();

            $this->dbCommit('관리자 - 회원정보 수정');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '회원정보가 수정 되었습니다.',
                'winClose' => $this->ajaxActionWinClose(true),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    public function userDelete(Request $request)
    {
        $this->transaction();

        try {
            $user = User::findOrFail($request->sid);
            $user->delete();

            $this->dbCommit('관리자 - 회원 삭제');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '삭제 되었습니다.',
                'location' => $this->ajaxActionLocation('reload'),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function userLogin(Request $request)
    {
        $user = User::findOrFail($request->sid);
        auth('web')->login($user);

        return $this->returnJsonData('location', $this->ajaxActionLocation('blank', env('APP_URL')));
    }

    private function passwordReset(Request $request)
    {
        $this->transaction();

        try {
            $reset_pw = 'ntta';

            $user = User::findOrFail($request->sid);
            $user->password = Hash::make($reset_pw);
            $user->update();

            $this->dbCommit('관리자 회원 비밀번호 초기화');

            return $this->returnJsonData('alert', [
                'msg' => "비밀번호 초기화 되었습니다.\n초기화 비밀번호 : {$reset_pw}"
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function dbChange(Request $request)
    {
        $this->transaction();

        try {
            $user = User::findOrFail($request->sid);
            $user->{$request->field} = $request->value;
            $user->update();

            $this->dbCommit('관리자 회원정보 부분 수정');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '변경 되었습니다.',
                'location' => $this->ajaxActionLocation('reload')
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }
}
