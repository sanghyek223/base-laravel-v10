@extends('admin.layouts.admin-layout')

@section('addStyle')
@endsection

@section('contents')
    <div class="sub-contents">
        <div class="sub-tit-wrap">
            <h3 class="sub-tit">[ {{ $mail->subject }} ] 발송 내역 상세보기</h3>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div style="font-weight: 600;">
                [
                대기: <span style="color: #eb5e00 !important;">{{ number_format($mail->readyCnt) }}</span> /
                성공: <span style="color: #0e9ad0 !important;">{{ number_format($mail->sucCnt) }}</span> /
                실패: <span style="color: #eb1600 !important;">{{ number_format($mail->failCnt) }}</span>
                ]
            </div>

            <a href="{{ route('mail') }}" class="btn btn-small btn-type1 color-type20">목록 으로</a>
        </div>

        <div class="table-wrap" style="margin-top: 10px;">
            <table class="cst-table list-table">
                <caption class="hide">목록</caption>

                <colgroup>
                    <col style="width: 5%;">
                    <col style="width: 15%;">
                    <col style="width: 25%;">
                    <col style="width: 15%;">
                    <col>
                    <col style="width: 8%;">
                </colgroup>

                <thead>
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">수신자</th>
                    <th scope="col">수신 이메일</th>
                    <th scope="col">발송 상태</th>
                    <th scope="col">상태 메세지</th>
                    <th scope="col">재발송</th>
                </tr>
                </thead>

                <tbody>
                @forelse($list as $row)
                    <tr data-sid="{{ $row->sid }}">
                        <td>{{ $row->seq }}</td>
                        <td>{{ $row->receiver_name }}</td>
                        <td>{{ $row->receiver_email }}</td>
                        <td>
                            @if($row->status === 'S')
                                <span style="color: #0e9ad0 !important;">성공</span>
                            @elseif($row->status === 'F')
                                <span class="fcRed">실패</span>
                            @else
                                <span style="color: #eb5e00!important;">발송중</span>
                            @endif
                        </td>
                        <td>{{ $row->status_msg }}</td>
                        <td>
                            <a href="javascript:void(0);" class="btn btn-small color-type15 re-mail">재발송</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">발송 내역이 없습니다.</td>
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

        $(document).on('click', '.re-mail', function() {
            const ajaxData = {
                'sid': getPK(this),
                'case': 'target-send',
            };

            if (confirm('메일을 재발송 하시겠습니까?')) {
                callAjax(dataUrl, ajaxData);
            }
        });
    </script>
@endsection
