<?php

namespace App\Services\Main;

use App\Models\Board;
use App\Services\AppServices;
use Illuminate\Http\Request;

/**
 * Class MainServices
 * @package App\Services
 */
class MainServices extends AppServices
{
    public function indexService(Request $request)
    {
        $this->data['notice'] = $this->mainBoards('notice')->orderByDesc('sid')->get(); // 공지사항
        $this->data['rollingPopups'] = $this->mainPopups()->get(); // 게시판 Rolling 팝업
//        $this->data['layerPopups'] = $this->mainPopups()->get(); // 게시판 Layer 팝업

        return $this->data;
    }

    public function dataAction(Request $request)
    {
        return match ($request->case) {
            default        => notFoundRedirect(),
        };
    }

    private function mainBoards($code, $limit = 5) // 메인에 노출되는 게시판 리스트
    {
        $query = Board::withCount('files')->where(['code' => $code, 'hide' => 'N', 'main' => 'Y']);

        if ($limit) {
            $query->limit($limit);
        }

        return $query;
    }

    private function mainPopups() // 메인에 노출되는 팝업 리스트
    {
        $today = now()->format('Y-m-d');
        $exception = [];

        foreach (request()->cookies->all() as $key => $val) {
            // 게시판 팝업 오늘하루 보지않기 있는지 체크
            if (strpos($key, 'board-popup-') !== false) {
                $boardSid = (int)str_replace('board-popup-', '', $key);
                $exception[] = $boardSid;
            }
        }

        return Board::withCount('files')
            ->where(['hide' => 'N', 'popup' => 'Y'])
            ->whereNotIn('sid', $exception)
            ->whereHas('popups', function ($q) use ($today) {
                $q->where('popup_sDate', '<=', $today)
                    ->where('popup_eDate', '>=', $today);

            });
    }
}
