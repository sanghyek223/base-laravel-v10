<?php

namespace App\Services\Board;

use App\Models\Board;
use App\Models\BoardReply;
use App\Models\BoardReplyCounter;
use App\Services\AppServices;
use Illuminate\Http\Request;

/**
 * Class ReplyServices
 * @package App\Services
 */
class ReplyServices extends AppServices
{
    public function upsertService(Request $request)
    {
        $sid = $request->sid;
        $b_sid = $request->b_sid;

        $this->data['board'] = Board::findOrFail($b_sid);
        $this->data['reply'] = empty($sid)
            ? null
            : BoardReply::where('b_sid', $b_sid)->withCount('files')->findOrFail($sid);

        return $this->data;
    }

    public function viewService(Request $request)
    {
        $sid = $request->sid;
        $b_sid = $request->b_sid;

        $this->data['board'] = Board::findOrFail($b_sid);
        $this->data['reply'] = BoardReply::where('b_sid', $b_sid)->withCount('files')->findOrFail($sid);
        $this->refCounter($request); // 조회수 업데이트

        return $this->data;
    }

    public function dataAction(Request $request)
    {
        return match ($request->case) {
            'reply-create' => $this->replyCreate($request),
            'reply-update' => $this->replyUpdate($request),
            'reply-delete' => $this->replyDelete($request),
            'db-change' => $this->dbChange($request),
            default => notFoundRedirect(),
        };
    }

    private function listUrl()
    {
        return route('board', ['code' => request()->code]);
    }

    private function replyCreate(Request $request)
    {
        $this->transaction();

        try {
            $reply = new BoardReply();
            $reply->setByData($request);
            $reply->save();

            $this->dbCommit("답글 등록");

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '답글이 등록 되었습니다.',
                'location' => $this->ajaxActionLocation('replace', $this->listUrl()),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function replyUpdate(Request $request)
    {
        $this->transaction();

        try {
            $reply = BoardReply::where('b_sid', $request->b_sid)->findOrFail($request->sid);
            $reply->setByData($request);
            $reply->update();

            $this->dbCommit('답글 수정');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '답글이 수정 되었습니다.',
                'location' => $this->ajaxActionLocation('replace', $this->listUrl()),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function replyDelete(Request $request)
    {
        $this->transaction();

        try {
            $reply = BoardReply::where('b_sid', $request->b_sid)->findOrFail($request->sid);
            $reply->delete();

            $this->dbCommit('답글 삭제');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '답글이 삭제 되었습니다.',
                'location' => $this->ajaxActionLocation('replace', $this->listUrl()),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function dbChange(Request $request)
    {
        $this->transaction();

        try {
            $reply = BoardReply::where('b_sid', $request->b_sid)->findOrFail($request->sid);
            $reply->{$request->column} = $request->value;
            $reply->update();

            $this->dbCommit('답글 부분 수정');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '수정 되었습니다.',
                'location' => $this->ajaxActionLocation('reload'),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function refCounter(Request $request)
    {
        // ip 기준으로 조회수 하루에 한번씩
        $check = BoardReplyCounter::whereRaw("DATE_FORMAT(created_at, '%Y%m%d') = ?", [now()->format('Ymd')])
            ->where([
                'br_sid' => $request->sid,
                'ip' => $request->ip()
            ])->exists();


        if (!$check) {
            $boardCounter = new BoardReplyCounter();
            $boardCounter->setByData($request);
            $boardCounter->save();

            $this->data['reply']->increment('ref');
        }
    }
}
