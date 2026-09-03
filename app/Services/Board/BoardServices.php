<?php

namespace App\Services\Board;

use App\Models\Board;
use App\Models\BoardFile;
use App\Models\BoardCounter;
use App\Models\BoardComment;
use App\Services\AppServices;
use Illuminate\Http\Request;

/**
 * Class BoardServices
 * @package App\Services
 */
class BoardServices extends AppServices
{
    private $boardConfig;
    private $boardCode;
    private $listUrl;

    public function __construct()
    {
        $this->boardCode = request()->code;
        $this->listUrl = route('board', ['code' => $this->boardCode]);

        $this->boardConfig = (new Board())->getBoardConfig();
    }

    private function defaultQuery($count = false)
    {
        $query = Board::where([
            'code' => $this->boardCode,
        ]);

        if ($count) {
            $query->withCount('files', 'comments');
        }

        return $query;
    }

    private function getNoticeList()
    {
        $noticeQuery = $this->defaultQuery(true)
            ->where('notice', 'Y')
            ->when(!isAdmin(), function ($query) {
                $query->where('hide', 'N');
            });

        return $noticeQuery->orderByDesc('sid')->limit('10')->get();
    }

    public function listService(Request $request)
    {
        $search = $request->search;
        $keyword = $request->keyword;

        $query = $this->defaultQuery(true)->orderByDesc('sid');

        // 공지사항 사용
        if ($this->boardConfig['use']['notice']) {
            $notice = $this->getNoticeList(); // 공지사항

            // 공지사항 있다면 제외
            if ($notice->isNotEmpty()) {
                $query->whereNotIn('sid', $notice->pluck('sid'));
            }
        }

        if ($request->category) {
            $query->where('category', $request->category);
        }

        if ($search && $keyword) {

            switch ($search) {
                case 'subject/contents':
                    $query->where(function ($q) use ($keyword) {
                        $q->where('subject', 'like', "%{$keyword}%")
                            ->orWhere('contents', 'like', "%{$keyword}%");
                    });
                    break;

                default:
                    $query->where($search, 'like', "%{$keyword}%");
                    break;
            }

        }

        $list = $query->paginate($this->boardConfig['paginate'])->appends($request->query());

        $this->data['list'] = setListSeq($list);
        $this->data['notice'] = $notice ?? [];

        return $this->data;
    }

    public function upsertService(Request $request)
    {
        $sid = $request->sid ?? null;

        $this->data['board'] = empty($sid) ? null : $this->defaultQuery(true)->findOrFail($sid);
        $this->data['popup'] = $this->data['board']?->popups;

        return $this->data;
    }

    public function viewService(Request $request)
    {
        $sid = $request->sid;

        $board = $this->defaultQuery(true)->findOrFail($sid);
        $this->refCounter($request); // 조회수 업데이트

        $this->data['board'] = $board;
        $this->data['prevBoard'] = $this->defaultQuery()->where('sid', '>', $sid)->orderBy('sid', 'asc')->first();
        $this->data['nextBoard'] = $this->defaultQuery()->where('sid', '<', $sid)->orderBy('sid', 'desc')->first();

        // 댓글 사용시
        if ($this->boardConfig['use']['comment']) {
            $this->data['comments'] = $this->data['board']->comments()
                ->where([
                    'depth1' => 0,
                    'depth2' => 0,
                ])->paginate($this->boardConfig['comment_paginate'])->appends($request->query());
        }

        return $this->data;
    }

    public function dataAction(Request $request)
    {
        return match ($request->case) {
            'board-create' => $this->boardCreate($request),
            'board-update' => $this->boardUpdate($request),
            'board-delete' => $this->boardDelete($request),
            'db-change' => $this->dbChange($request),

            'popup-layer-preview' => $this->popupLayerPreview($request),
            'popup-rolling-preview' => $this->popupRollingPreview($request),

            'comment-postform' => $this->commentPostform($request),
            'comment-create' => $this->commentCreate($request),
            'comment-update' => $this->commentUpdate($request),
            'comment-delete' => $this->commentDelete($request),
            default => notFoundRedirect(),
        };
    }

    private function boardCreate(Request $request)
    {
        $this->transaction();

        try {
            $board = new Board();
            $board->setByData($request);
            $board->save();

            $this->dbCommit("게시글 등록");

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '게시글이 등록 되었습니다.',
                'location' => $this->ajaxActionLocation('replace', $this->listUrl),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function boardUpdate(Request $request)
    {
        $this->transaction();

        try {
            $board = $this->defaultQuery()->findOrFail($request->sid);
            $board->setByData($request);
            $board->update();

            $this->dbCommit('게시글 수정');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '게시글이 수정 되었습니다.',
                'location' => $this->ajaxActionLocation('replace', $this->listUrl),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function boardDelete(Request $request)
    {
        $this->transaction();

        try {
            $board = $this->defaultQuery()->findOrFail($request->sid);
            $board->delete();

            $this->dbCommit('게시글 삭제');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '게시글이 삭제 되었습니다.',
                'location' => $this->ajaxActionLocation('replace', $this->listUrl),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function dbChange(Request $request)
    {
        $this->transaction();

        try {
            $board = $this->defaultQuery()->findOrFail($request->sid);
            $board->{$request->column} = $request->value;
            $board->update();

            $this->dbCommit('게시글 부분 수정');

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '수정 되었습니다.',
                'location' => $this->ajaxActionLocation('reload'),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function popupLayerPreview(Request $request)
    {
        $files = [];
        $popupSkin = $request->popup_skin;

        if ($request->sid != 0) {
            foreach (BoardFile::where('b_sid', $request->sid)->whereNotIn('sid', $request->plupload_file_del ?? [])->get() as $row) {
                $files[] = (object)['filename' => $row->filename, 'download' => $row->download];
            }
        }

        foreach ($request->plupload ?? [] as $key => $val) {
            $files[] = (object)['filename' => $val, 'download' => 0];
        }

        $board = (object)$request->all();
        $board->preview = true;
        $board->files = $files;
        $board->files_count = count($files);
        $board->popups = (object)[
            'width' => $request->width ?? 500,
            'height' => $request->height ?? 600,
            'position_x' => $request->position_x ?? 0,
            'position_y' => $request->position_y ?? 0,
            'popup_detail' => $request->popup_detail ?? '',
            'popup_link' => $request->popup_link ?? '',
            'popup_skin' => $popupSkin,
            'popup_contents' => ($request->popup_select == '1') ? $request->contents : $request->popup_contents,
        ];

        $this->data['layerPopups'][] = $board;

        return $this->returnJsonData('append', [
            $this->ajaxActionHtml('body', view("popup.board.layer-popup", $this->data)->render()),
        ]);
    }

    private function popupRollingPreview(Request $request)
    {
        $files = [];
        $popupSkin = $request->popup_skin;

        if ($request->sid != 0) {
            foreach (BoardFile::where('b_sid', $request->sid)->whereNotIn('sid', $request->plupload_file_del ?? [])->get() as $row) {
                $files[] = (object)['filename' => $row->filename, 'download' => $row->download];
            }
        }

        foreach ($request->plupload ?? [] as $key => $val) {
            $files[] = (object)['filename' => $val, 'download' => 0];
        }

        $board = (object)$request->all();
        $board->preview = true;
        $board->files = $files;
        $board->files_count = count($files);
        $board->popups = (object)[
            'popup_detail' => $request->popup_detail ?? '',
            'popup_link' => $request->popup_link ?? '',
            'popup_skin' => $popupSkin,
            'popup_contents' => ($request->popup_select == '1') ? $request->contents : $request->popup_contents,
        ];

        $this->data['rollingPopups'][] = $board;

        return $this->returnJsonData('append', [
            $this->ajaxActionHtml('body', view("popup.board.rolling-popup", $this->data)->render()),
        ]);
    }

    private function commentPostform(Request $request)
    {
        $sid = $request->sid;
        $b_sid = $request->b_sid;
        $action = $request->action;

        switch ($action) {
            case 'create': // 등록
                $reqDepth1 = $request->depth1;
                $reqDepth2 = $request->depth2;

                $depth1 = $reqDepth1;
                $depth2 = 0;

                if ($depth1 == 0) {
                    $depth1 = $sid;
                }

                if ($reqDepth1 != 0 && $reqDepth2 == 0) {
                    $depth2 = $sid;
                }

                $comment = (object)[
                    'depth1' => $depth1, // 1차 상위 댓글 sid
                    'depth2' => $depth2, // 2차 상위 댓글 sid
                ];
                break;

            case 'update': // 수정
                $comment = BoardComment::where('b_sid', $b_sid)->findOrFail($sid);
                break;

            default:
                return notFoundRedirect();
        }

        $this->data['action'] = $action;
        $this->data['comment'] = $comment;

        $view = view("board.{$this->boardCode}.comment.upsert", $this->data)->render();

        return $this->returnJsonData('upsert', $view);
    }

    private function commentCreate(Request $request)
    {
        $this->transaction();

        try {
            $comment = new BoardComment();
            $comment->setByData($request);
            $comment->save();

            $this->dbCommit("댓글 등록");

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '댓글이 등록 되었습니다.',
                'location' => $this->ajaxActionLocation('reload'),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function commentUpdate(Request $request)
    {
        $this->transaction();

        try {
            $comment = BoardComment::where('b_sid', $request->b_sid)->findOrFail($request->sid);
            $comment->setByData($request);
            $comment->update();

            $this->dbCommit("댓글 수정");

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '댓글이 수정 되었습니다.',
                'location' => $this->ajaxActionLocation('reload'),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function commentDelete(Request $request)
    {
        $this->transaction();

        try {
            $comment = BoardComment::where('b_sid', $request->b_sid)->findOrFail($request->sid);
            $comment->delete();

            $this->dbCommit("댓글 삭제");

            return $this->returnJsonData('alert', [
                'case' => true,
                'msg' => '댓글이 삭제 되었습니다.',
                'location' => $this->ajaxActionLocation('reload'),
            ]);
        } catch (\Exception $e) {
            return $this->dbRollback($e);
        }
    }

    private function refCounter(Request $request)
    {
        // ip 기준으로 조회수 하루에 한번씩
        $check = BoardCounter::whereRaw("DATE_FORMAT(created_at, '%Y%m%d') = ?", [now()->format('Ymd')])
            ->where([
                'b_sid' => $request->sid,
                'ip' => $request->ip()
            ])->exists();


        if (!$check) {
            $boardCounter = new BoardCounter();
            $boardCounter->setByData($request);
            $boardCounter->save();

            $this->data['board']->increment('ref');
        }
    }
}
