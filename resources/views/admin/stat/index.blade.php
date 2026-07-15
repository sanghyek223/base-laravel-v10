@php
    function getGrpSize($cnt, $total, $max, $width)
    {
        //현재수, 전체수, 가장큰수, 원하는길이
        if (!$width) return @intval($cnt/$total*100);

        if ($cnt) {
            return ($cnt == $max) ? $width : @intval($cnt / $max * $width);
        } else {
            return 0;
        }
    }
@endphp

@extends('admin.layouts.admin-layout')

@section('addStyle')
@endsection

@section('contents')
    <div class="sub-contents">
        <div class="text-right">
            <a href="{{ route('stat.referer') }}" class="btn btn-small btn-type1 color-type20">
                접속 경로
            </a>
        </div>

        <div class="table-wrap" style="display: flex; margin-top: 10px;">
            <table class="cst-table list-table" style="margin: 15px;">
                <caption class="hide">목록</caption>

                <colgroup>
                    <col style="width: 16%">
                    <col style="width: 21%">
                    <col style="width: 21%">
                    <col style="width: 21%">
                    <col style="width: 21%">
                </colgroup>

                <thead>
                <tr>
                    <th scope="col">
                        <select id="yearSelect" style="width: 99%; text-align: center;">
                            @for($i = 2024; $i <= (int)date('Y'); $i++)
                                <option value="{{ $i }}" {{ $i == $year ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </th>
                    <th scope="col">접속자</th>
                    <th scope="col">(누적)</th>
                    <th scope="col">페이지뷰</th>
                    <th scope="col">(누적)</th>
                </tr>
                </thead>

                <tbody>
                @php
                    $m_accessor_acc = 0; // 접속자 누적
                    $m_page_acc = 0; // 페이지 누적
                @endphp

                @foreach($statConfig['month'] as $key => $val)
                    @php
                        $hit = $stat['month'][$key]->hit ?? 0;
                        $page = $stat['month'][$key]->page ?? 0;
                        $m_accessor_acc = ($m_accessor_acc + $hit ?? 0);
                        $m_page_acc = ($m_page_acc + $page ?? 0);
                    @endphp

                    <tr>
                        <th>{{ substr($val, 0, 3) }}</th>
                        <td>{{ empty($hit) ? '' : number_format($hit) }}</td>
                        <td>{{ empty($hit) ? '' : number_format($m_accessor_acc) }}</td>
                        <td>{{ empty($page) ? '' : number_format($page) }}</td>
                        <td>{{ empty($page) ? '' : number_format($m_page_acc) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <table class="cst-table list-table" style="margin: 15px;">
                <caption class="hide">목록</caption>

                <colgroup>
                    <col style="width: 15%">
                    <col style="width: 15%">
                    <col style="width: 17%">
                    <col style="width: 17%">
                    <col style="width: 17%">
                    <col style="width: 17%">
                </colgroup>

                <thead>
                <tr>
                    <th scope="col" colspan="2">
                        <select id="monthSelect" style="width: 100%; text-align: center;">
                            @foreach($statConfig['month'] as $key => $val)
                                <option value="{{ $key }}" {{ $key == $month ? 'selected' : '' }}>{{ $val }}</option>
                            @endforeach
                        </select>
                    </th>
                    <th scope="col">접속자</th>
                    <th scope="col">(누적)</th>
                    <th scope="col">페이지뷰</th>
                    <th scope="col">(누적)</th>
                </tr>
                </thead>

                <tbody>
                @php
                    $d_accessor_acc = 0; // 접속자 누적
                    $d_page_acc = 0; // 페이지 누적
                @endphp

                @for($i = 1; $i <= $daysInMonth; $i++)
                    @php
                        $addDay = addZero($i, 2);
                        $hit = $stat['day'][$addDay]->hit ?? 0;
                        $page = $stat['day'][$addDay]->page ?? 0;
                        $d_accessor_acc = ($d_accessor_acc + $hit ?? 0);
                        $d_page_acc = ($d_page_acc + $page ?? 0);
                    @endphp

                    <tr>
                        <th>{{ $addDay }}</th>
                        <th>{{ getYoil(date("{$year}-{$month}-{$addDay}")) }}</th>
                        <td>{{ empty($hit) ? '' : number_format($hit) }}</td>
                        <td>{{ empty($hit) ? '' : number_format($d_accessor_acc) }}</td>
                        <td>{{ empty($page) ? '' : number_format($page) }}</td>
                        <td>{{ empty($page) ? '' : number_format($d_page_acc) }}</td>
                    </tr>
                @endfor
                </tbody>
            </table>

            <table class="cst-table list-table" style="margin: 15px;">
                <caption class="hide">목록</caption>

                <colgroup>
                    <col style="width: 15%">
                    <col style="width: 15%">
                    <col style="width: 17%">
                    <col>
                </colgroup>

                <thead>
                <tr>
                    <th class="scope" colspan="2">
                        <select id="daySelect" style="width: 99%; text-align: center;">
                            @for($i = 1; $i <= date('t', mktime(0, 0, 0, $month, 1, $year)); $i++)
                                <option value="{{ addZero($i, 2) }}" {{ addZero($i, 2) == $day ? 'selected' : '' }}>{{ addZero($i, 2) }}({{ getYoil(date("{$year}-{$month}-" .  addZero($i, 2))) }})</option>
                            @endfor
                        </select>
                    </th>
                    <th class="scope">접속자</th>
                    <th class="scope">그래프</th>
                </tr>
                </thead>

                <tbody>
                @php
                    $SUM_HMDX = 0;
                    $MAX_HMDX = 0;

                    foreach ($stat['graph'] as $row) {
                        $SUM_HMDX += $row->cnt ?? 0;

                        if($MAX_HMDX < $row->cnt) {
                            $MAX_HMDX = $row->cnt;
                        }
                    }
                @endphp

                @for($i = 0; $i < 24; $i++)
                    @php
                        $addTime = addZero($i, 2);
                        $timeCnt = $stat['graph'][$addTime]->cnt ?? 0;
                    @endphp
                    <tr>
                        @if($i === 0)
                            <th rowspan="12">AM</th>
                        @endif

                        @if($i === 12)
                            <th rowspan="12">PM</th>
                        @endif

                        <th style="border-left:1px solid #dcdcdc !important;">{{ $addTime }}</th>
                        <td>
                            @if(!empty($timeCnt))
                                {{ number_format($timeCnt) }}
                                <span style="color:#c0c0c0;font-family:arial;font-size:10px;">({{ round($timeCnt / $SUM_HMDX * 100) }}%)</span>
                            @endif
                        </td>
                        <td>
                            <div style="width: {{ getGrpSize($timeCnt, $SUM_HMDX, $MAX_HMDX,100) }}%;height:10px;background:#194165;margin:0 3px 0 0;float:left;vertical-align:middle;"></div>
                        </td>
                    </tr>
                @endfor
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('addScript')
    <script>
        $(document).on('change', '#yearSelect', function() {
            let url = '{{ route('stat') }}';
            url = (url + '?year=' + $(this).val());
            location.replace(url);
        });

        $(document).on('change', '#monthSelect', function() {
            let url = '{!! route('stat', ['year' => $year]) !!}';
            url = (url + '&month=' + $(this).val());
            location.replace(url);
        });

        $(document).on('change', '#daySelect', function() {
            let url = '{!! route('stat', ['year' => $year, 'month' => $month]) !!}';
            url = (url + '&day=' + $(this).val());
            location.replace(url);
        });
    </script>
@endsection
