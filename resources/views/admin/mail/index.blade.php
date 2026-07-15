@extends('admin.layouts.admin-layout')

@section('addStyle')
@endsection

@section('contents')
    <div class="sub-contents">
        <form id="searchF" name="searchF" action="{{ route('mail') }}" class="sch-form-wrap">
            <fieldset>
                <legend class="hide">검색</legend>
                <div class="table-wrap">
                    <table class="cst-table">
                        <colgroup>
                            <col style="width: 20%;">
                            <col style="width: 30%;">
                            <col style="width: 20%;">
                            <col style="width: 30%;">
                        </colgroup>

                        <tbody>
                        <tr>
                            <th scope="row">메일 제목</th>
                            <td class="text-left">
                                <input type="text" name="subject" value="{{ request()->subject ?? '' }}" class="form-item">
                            </td>

                            <th scope="row">발송자명</th>
                            <td class="text-left">
                                <input type="text" name="sender_name" value="{{ request()->sender_name ?? '' }}" class="form-item">
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">작성일</th>
                            <td class="text-left">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <input type="text" name="create_sdate" value="{{ request()->create_sdate ?? '' }}" class="form-item" readonly datepicker style="width: 47%">

                                    <span>~</span>

                                    <input type="text" name="create_edate" value="{{ request()->create_edate ?? '' }}" class="form-item" readonly datepicker style="width: 47%">
                                </div>
                            </td>

                            <th scope="row">발송일</th>
                            <td class="text-left">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <input type="text" name="send_sdate" value="{{ request()->send_sdate ?? '' }}" class="form-item" readonly datepicker style="width: 47%">

                                    <span>~</span>

                                    <input type="text" name="send_edate" value="{{ request()->send_edate ?? '' }}" class="form-item" readonly datepicker style="width: 47%">
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="btn-wrap text-center">
                    <button type="submit" class="btn btn-type1 color-type17">검색</button>
                    <a href="{{ route('mail') }}" class="btn btn-type1 color-type18">검색 초기화</a>
                </div>
            </fieldset>
        </form>

        <div class="list-contop text-right cf">
            <span class="cnt full-left">
                [총 <strong>{{ number_format($list->total()) }}</strong> 건]
            </span>

            <a href="{{ route('mail.upsert') }}" class="btn btn-small btn-type1 color-type20 call-popup" data-name="mail-upsert" data-width="850" data-height="900">
                메일 등록
            </a>
        </div>

        <div class="table-wrap" style="margin-top: 10px;">
            <table class="cst-table list-table">
                <caption class="hide">목록</caption>

                <colgroup>
                    <col style="width: 4%;">
                    <col>
                    <col style="width: 9%;">
                    <col style="width: 5%;">
                    <col style="width: 9%;">
                    <col style="width: 7%;">
                    <col style="width: 5%;">
                    <col style="width: 5%;">
                    <col style="width: 6%;">
                    <col style="width: 6%;">
                    <col style="width: 6%;">
                    <col style="width: 5%;">
                </colgroup>

                <thead>
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">제목</th>
                    <th scope="col">발송자명</th>
                    <th scope="col">총 발송 건수</th>
                    <th scope="col">대기 / 성공 / 실패</th>
                    <th scope="col">발송 상태 갱신</th>
                    <th scope="col">발송횟수</th>
                    <th scope="col">재발송</th>
                    <th scope="col">작성일</th>
                    <th scope="col">최종 발송일</th>
                    <th scope="col">상세보기</th>
                    <th scope="col">관리</th>
                </tr>
                </thead>

                <tbody>
                @forelse($list as $row)
                    <tr data-sid="{{ $row->sid }}">
                        <td>{{ $row->seq }}</td>
                        <td>
                            <a href="{{ route('mail.preview', ['sid' => $row->sid]) }}" class="call-popup" data-name="mail-preview" data-width="700" data-height="650">
                                <b>{{ $row->subject }}</b>
                            </a>
                        </td>
                        <td>{{ $row->sender_name }}</td>
                        <td>{{ number_format($row->totCnt()) }}</td>
                        <td>
                            <span style="color: #eb5e00 !important;">{{ number_format($row->readyCnt) }}</span> /
                            <span style="color: #0e9ad0 !important;">{{ number_format($row->sucCnt) }}</span> /
                            <span style="color: #eb1600 !important;">{{ number_format($row->failCnt) }}</span>
                        </td>
                        <td>
                            @if($row->readyCnt > 0)
                                <a href="javascript:void(0);" class="btn btn-small color-type1 send-renew">갱신</a>
                            @endif
                        </td>
                        <td>{{ number_format($row->thread) }}</td>
                        <td>
                            <a href="javascript:void(0);" class="btn btn-small color-type15 mail-send">{{ empty($row->send_date) ? '발송' : '재발송' }}</a>
                        </td>
                        <td>{{ $row->created_at }}</td>
                        <td>{{ empty($row->send_date) ? '' : $row->send_date }}</td>
                        <td><a href="{{ route('mail.detail', ['sid' => $row->sid]) }}" class="btn btn-small color-type10" style="margin-top: 5px;">상세보기</a></td>
                        <td>
                            <a href="{{ route('mail.upsert', ['sid' => $row->sid]) }}" class="btn-admin call-popup" data-name="mail-upsert" data-width="850" data-height="900">
                                <img src="/assets_admin/image/ic_modify.png" alt="수정">
                            </a>

                            <a href="javascript:void(0);" class="btn-admin btn-del">
                                <img src="/assets_admin/image/ic_del.png" alt="삭제">
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12">등록된 메일이 없습니다.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{ $list->links('pagination::custom') }}
    </div>
@endsection

@section('addScript')
    <script>
        const dataUrl = '{{ route('mail.data') }}';

        const getPK = (_this) => {
            return $(_this).closest('tr').data('sid');
        }

        $(document).on('click', '.send-renew', function() {
            if (confirm('발송상태 갱신시 시간이 소요될수 있습니다. 잠시만 기다려 주세요.')) {
                callAjax(dataUrl, {
                    'case': 'send-renew',
                    'sid': getPK(this),
                });
            }
        });

        $(document).on('click', '.mail-send', function() {
            const ajaxData = {
                'sid': getPK(this),
                'case': 'mail-send',
            };

            if (confirm(`메일을 ${$(this).html()} 하시겠습니까?`)) {
                callAjax(dataUrl, ajaxData);
            }
        });

        $(document).on('click', '.btn-del', function() {
            const ajaxData = {
                'sid': getPK(this),
                'case': 'mail-delete',
            };

            if (confirm('삭제 하시겠습니까?')) {
                callAjax(dataUrl, ajaxData);
            }
        });
    </script>
@endsection
