<?php

namespace App\Services\Admin\Mail;

use App\Models\MailAddress;
use App\Models\MailAddressDetail;
use App\Services\AppServices;
use Illuminate\Http\Request;

/**
 * Class MailAddressServices
 * @package App\Services
 */
class MailAddressServices extends AppServices
{
    public function indexService(Request $request)
    {
        $query = MailAddress::withCount('list')->orderByDesc('sid');

        if($request->title) {
            $query->where('title', 'LIKE', "%{$request->title}%");
        }

        $list = $query->paginate(20)->appends($request->query());
        $this->data['list'] = setListSeq($list);

        return $this->data;
    }

    public function upsertService(Request $request)
    {
        $sid = $request->sid;
        $this->data['address'] = empty($sid) ? [] : MailAddress::findOrFail($sid);

        return $this->data;
    }

    public function dataAction(Request $request)
    {
        switch ($request->case) {
            case 'address-create':
                return $this->addressCreate($request);

            case 'address-update':
                return $this->addressUpdate($request);

            case 'address-delete':
                return $this->addressDelete($request);

            default:
                return notFoundRedirect();
        }
    }

    private function addressCreate(Request $request)
    {
        $this->transaction();

        try {
            $address = (new MailAddress());
            $address->setByData($request);
            $address->save();

            $this->dbCommit('관리자 메일 주소록 생성');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => "생성 되었습니다.",
                'winClose' => $this->ajaxActionWinClose(true)
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function addressUpdate(Request $request)
    {
        $this->transaction();

        try {
            $address = MailAddress::findOrFail($request->sid);
            $address->setByData($request);
            $address->update();

            $this->dbCommit('관리자 메일 주소록 수정');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => "수정 되었습니다.",
                'winClose' => $this->ajaxActionWinClose(true)
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function addressDelete(Request $request)
    {
        $this->transaction();

        try {
            $address = MailAddress::findOrFail($request->sid);
            $address->delete();

            $this->dbCommit('관리자 메일 주소록 삭제');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => "삭제 되었습니다.",
                'location' => $this->ajaxActionLocation('reload')
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }
}