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
class MailAddressDetailServices extends AppServices
{
    public function indexService(Request $request)
    {
        $address = MailAddress::findOrFail($request->ma_sid);

        $query = $address->list()->orderByDesc('sid');

        if (!empty($request->keyfield) && !empty($request->keyword)) {
            $query->where($request->keyfield, 'LIKE', "%{$request->keyword}%");
        }

        $list = $query->paginate(20)->appends($request->query());

        $this->data['list'] = setListSeq($list);
        $this->data['address'] = $address;

        return $this->data;
    }

    public function upsertService(Request $request)
    {
        $sid = $request->sid;
        $address = MailAddress::findOrFail($request->ma_sid);

        $this->data['address'] = $address;
        $this->data['addressDetail'] = empty($sid) ? null : $address->list()->findOrFail($sid);

        return $this->data;
    }

    public function dataAction(Request $request)
    {
        switch ($request->case) {
            case 'collective-create':
                return $this->collectiveCreate($request);

            case 'individual-create':
                return $this->individualCreate($request);

            case 'individual-update':
                return $this->individualUpdate($request);

            case 'addressDetail-delete':
                return $this->listDelete($request);

            default:
                return notFoundRedirect();
        }
    }

    private function collectiveCreate(Request $request)
    {
        $this->transaction();

        try {
            foreach (json_decode($request->data) ?? [] as $data) {
                if (!empty($data->name) && !empty($data->email)) {
                    $data->ma_sid = $request->ma_sid;

                    $addressDetail = (new MailAddressDetail());
                    $addressDetail->setByData($data);
                    $addressDetail->save();
                }
            }

            $this->dbCommit('관리자 - 주소록 일괄 등록');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => "등록 되었습니다.",
                'winClose' => $this->ajaxActionWinClose(true)
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function individualCreate(Request $request)
    {
        $this->transaction();

        try {
            $addressDetail = (new MailAddressDetail());
            $addressDetail->setByData($request);
            $addressDetail->save();

            $this->dbCommit('관리자 - 주소록 개별 등록');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => "등록 되었습니다.",
                'winClose' => $this->ajaxActionWinClose(true)
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function individualUpdate(Request $request)
    {
        $this->transaction();

        try {
            $addressDetail = MailAddressDetail::findOrFail($request->sid);
            $addressDetail->setByData($request);
            $addressDetail->save();

            $this->dbCommit('관리자 - 주소록 개별 수정');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => "수정 되었습니다.",
                'winClose' => $this->ajaxActionWinClose(true)
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function listDelete(Request $request)
    {
        $this->transaction();

        try {
            $addressDetail = MailAddressDetail::findOrFail($request->sid);
            $addressDetail->delete();

            $this->dbCommit('관리자 - 주소록 명단 삭제');

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