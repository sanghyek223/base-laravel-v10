@extends('admin.layouts.admin-layout')

@section('addStyle')
@endsection

@section('contents')
    <div class="sub-contents">
        <form id="searchF" name="searchF" action="{{ route('stat.referer') }}" class="sch-form-wrap">
            <fieldset>
                <legend class="hide">검색</legend>
                <div class="table-wrap">
                    <table class="cst-table">
                        <colgroup>
                            <col style="width: 30%;">
                            <col>
                        </colgroup>

                        <tbody>
                        <tr>
                            <th scope="row">정렬조건</th>
                            <td class="text-left">
                                <select name="year" onchange="this.form.submit();" style="width: 32%; margin-bottom: 5px; text-align: center;">
                                    @for($i = 2024; $i <= (int)date('Y'); $i++)
                                        <option value="{{ $i }}" {{ $i == $year ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>

                                <select name="month" onchange="this.form.submit();" style="width: 32%; margin-right: 15px; margin-left: 15px; text-align: center;">
                                    @foreach($statConfig['month'] as $key => $val)
                                        <option value="{{ $key }}" {{ $month == $key ? 'selected' : '' }}>{{ $val }}</option>
                                    @endforeach
                                </select>

                                <select name="day" onchange="this.form.submit();" style="width: 32%; margin-bottom: 5px; text-align: center;">
                                    <option value="">- Daily -</option>
                                    @for($i = 1; $i <= date('t', mktime(0, 0, 0, $month, 1, $year)); $i++)
                                        @php
                                            $addDay = addZero($i, 2);
                                        @endphp
                                        <option value="{{ $addDay }}" {{ $day == $addDay ? 'selected' : '' }}>{{ $addDay }} ({{ getYoil(date("{$year}-{$month}-{$addDay}")) }})</option>
                                    @endfor
                                </select>

                                <div style="height:5px;"></div>

                                <select name="sort" onchange="this.form.submit();" style="width: 32%; text-align: center;">
                                    @foreach($statConfig['sort'] as $key => $val)
                                        <option value="{{ $key }}" {{ (request()->sort  ?? '') == $key ? 'selected' : '' }}>{{ $val }}</option>
                                    @endforeach
                                </select>

                                <select name="orderby" onchange="this.form.submit();" style="width: 32%; margin-right: 15px; margin-left: 15px; text-align: center;">
                                    @foreach($statConfig['orderby'] as $key => $val)
                                        <option value="{{ $key }}" {{ (request()->orderby  ?? '') == $key ? 'selected' : '' }}>{{ $val }}</option>
                                    @endforeach
                                </select>

                                <select name="recnum" onchange="this.form.submit();" style="width: 32%; text-align: center;">
                                    @foreach($statConfig['recnum'] as $key => $val)
                                        <option value="{{ $key }}" {{ (request()->recnum ?? '') == $key ? 'selected' : '' }}>{{ $val }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">검색</th>
                            <td>
                                <select name="search" style="width: 25%; text-align: center;">
                                    @foreach($statConfig['search'] as $key => $val)
                                        <option value="{{ $key }}" {{ (request()->search ?? '') == $key ? 'selected' : ''  }}>{{ $val }}</option>
                                    @endforeach
                                </select>

                                <input type="text" name="keyword" id="keyword" value="{{ request()->keyword ?? '' }}" class="form-item" style="width: 74%;">
                            </td>
                        </tr>

                        </tbody>
                    </table>
                </div>

                <div class="btn-wrap text-center">
                    <button type="submit" class="btn btn-type1 color-type17">검색</button>
                    <a href="{{ route('stat.referer') }}" class="btn btn-type1 color-type18">검색 초기화</a>
                </div>
            </fieldset>
        </form>

        <div class="text-right">
            <a href="{{ route('stat') }}" class="btn btn-small btn-type1 color-type20">
                접속 통계
            </a>
        </div>

        <div class="table-wrap" style="margin-top: 10px;">
            <table class="cst-table list-table">
                <caption class="hide">목록</caption>

                <colgroup>
                    <col style="width: 6%;">
                    <col>
                    <col style="width: 10%;">
                    <col style="width: 10%;">
                    <col style="width: 17%;">
                    <col style="width: 10%;">
                    <col style="width: 17%;">
                </colgroup>

                <thead>
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">접속경로</th>
                    <th scope="col">플랫폼</th>
                    <th scope="col">검색엔진</th>
                    <th scope="col">키워드</th>
                    <th scope="col">IP</th>
                    <th scope="col">접속일시</th>
                </tr>
                </thead>

                <tbody>
                @forelse($list as $row)
                    <tr>
                        <td>{{ $row->seq }}</td>
                        <td>
                            @if($row->referer == '직접접속')
                                <span style="color:#c0c0c0;">{{ $row->referer }}</span>
                            @else
                                <a href="{{ $row->referer }}" target="_blank" title="{{ $row->referer }}">{{ $row->referer }}</a>
                            @endif
                        </td>
                        <td>{{ $row->platform }}</td>
                        <td>{{ $row->browser }}</td>
                        <td>{{ $row->keyword }}</td>
                        <td>{{ $row->ip }}</td>
                        <td>{{ $row->created_at }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">로그 데이터가 없습니다.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $list->links('pagination::custom') }}
@endsection

@section('addScript')
@endsection
