<?php

namespace App\Services\Admin\Stat;

use Carbon\Carbon;
use App\Models\Referer;
use App\Models\Counter;
use App\Services\AppServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class StatServices
 * @package App\Services
 */
class StatServices extends AppServices
{
    public function indexService(Request $request)
    {
        $year = $request->year ?? now()->year;
        $month = $request->month ?? now()->format('m');
        $day = $request->day ?? (now()->format('Ym') === ($year . $month) ? now()->format('d') : '01');

        // 월별 데이터
        $monthData = Counter::selectRaw("
                SUM(hit) AS hit,
                SUM(page) AS page,
                LPAD(MONTH(created_at), 2, '0') AS month
            ")
            ->whereYear('created_at', $year)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->get()
            ->keyBy('month');

        // 일별 데이터
        $dayData = Counter::selectRaw("
                SUM(hit) AS hit,
                SUM(page) AS page,
                LPAD(DAY(created_at), 2, '0') AS day
            ")
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->groupBy(DB::raw('DAY(created_at)'))
            ->get()
            ->keyBy('day');

        // 시간별 데이터 (그래프)
        $graphData = Referer::selectRaw("
                COUNT(sid) AS cnt,
                LPAD(HOUR(created_at), 2, '0') AS hour
            ")
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->whereDay('created_at', $day)
            ->groupBy(DB::raw('HOUR(created_at)'))
            ->get()
            ->keyBy('hour');

        $this->data['year'] = $year;
        $this->data['month'] = $month;
        $this->data['day'] = $day;
        $this->data['daysInMonth'] = Carbon::createFromDate($year, $month)->daysInMonth;

        $this->data['stat'] = [
            'graph' => $graphData,
            'month' => $monthData,
            'day' => $dayData,
        ];

        return $this->data;
    }

    public function refererService(Request $request)
    {
        $year = $request->year ?? date('Y');
        $month = $request->month ?? date('m');
        $day = $request->day ?? '';

        $query = empty($day)
            ? Referer::whereYear('created_at', $year)->whereMonth('created_at', $month)
            : Referer::whereYear('created_at', $year)->whereMonth('created_at', $month)->whereDay('created_at', $day);

        if ($request->search && $request->keyword) {
            $query->where($request->search, 'LIKE', "%{$request->keyword}%");
        }

        if ($request->sort) {
            $query->orderBy($request->sort, ($request->orderby ?? 'DESC'));
        }

        $list = $query->paginate($request->recnum ?? 10)->appends($request->query());

        $this->data['list'] = setListSeq($list);
        $this->data['year'] = $year;
        $this->data['month'] = $month;
        $this->data['day'] = $day;

        return $this->data;
    }

    public function setCountService()
    {
        $ip = request()->ip();
        $cookie_ip = request()->cookie('mylogip');

        if (request()->server('HTTP_REFERER') && $cookie_ip == $ip) {
            Counter::whereDate('created_at', now()->toDateString())->increment('page');
        } else {
            if (is_null($cookie_ip)) {
                $this->setCounter();
                $this->setReferer();
            }
        }

        setcookie('mylogip', $ip, 0, '/');
    }

    private function setCounter()
    {
        $counter = Counter::whereDate('created_at', now()->toDateString())->first();

        if (is_null($counter)) {
            $counter = new Counter();
            $counter->setByData();
            $counter->save();
        } else {
            $counter->increment('hit');
            $counter->increment('page');
        }
    }

    private function setReferer()
    {
        $referer = new Referer();
        $referer->setByData();
        $referer->save();
    }

    public function dataAction(Request $request)
    {
        return match ($request->case) {
            default => notFoundRedirect(),
        };
    }
}
