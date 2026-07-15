<div class="table-wrap" style="margin-top: 10px;">
    <table class="cst-table list-table">
        <caption class="hide">목록</caption>

        <colgroup>
            <col style="width: 4%;">
            <col style="width: 5%;">
            <col style="width: 15%;">
            <col style="width: 10%;">
            <col style="width: 10%;">
            <col>
            <col style="width: 5.5%;">
            <col style="width: 5.5%;">
            <col style="width: 5.5%;">
        </colgroup>

        <thead>
        <tr>
            <th scope="col">No</th>
            <th scope="col">회원등급</th>
            <th scope="col">ID</th>
            <th scope="col">이름</th>
            <th scope="col">핸드폰</th>
            <th scope="col">이메일</th>
            <th scope="col">로그인</th>
            <th scope="col">비밀번호<br>초기화</th>
            <th scope="col">관리</th>
        </tr>
        </thead>

        <tbody>
        @forelse($list as $row)
            <tr data-sid="{{ $row->sid }}">
                <td>{{ number_format($row->seq) }}</td>
                <td>{{ $row->getLevel() }}</td>
                <td>{{ $row->uid ?? '' }}</td>
                <td>{{ $row->name_kr ?? '' }}</td>
                <td>{{ $row->mobile ?? '' }}</td>
                <td>{{ $row->email ?? '' }}</td>
                <td>
                    <a href="javascript:void(0);" class="btn btn-small color-type5 user-login">
                        로그인
                    </a>
                </td>
                <td>
                    <a href="javascript:void(0);" class="btn btn-small color-type18 pw-reset">
                        초기화
                    </a>
                </td>
                <td>
                    <a href="{{ route('member.upsert', ['sid' => $row->sid]) }}" class="btn-admin call-popup" data-name="member-upsert" data-width="850" data-height="900">
                        <img src="/assets_admin/image/ic_modify.png" alt="수정">
                    </a>

                    <a href="javascript:void(0);" class="btn-admin user-delete">
                        <img src="/assets_admin/image/ic_del.png" alt="삭제">
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9">등록된 회원이 없습니다.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

@section('list-script')
    <script>
        $(document).on('click', '.user-login', function() {
            callAjax(dataUrl, {
                'case': 'user-login',
                'sid': getPK(this),
            });
        });

        $(document).on('click', '.pw-reset', function() {
            callAjax(dataUrl, {
                'case': 'pw-reset',
                'sid': getPK(this),
            });
        });

        $(document).on('click', '.user-delete', function() {
            if (confirm('삭제 하시겠습니까?')) {
                callAjax(dataUrl, {
                    'case': 'user-delete',
                    'sid': getPK(this),
                });
            }
        });
    </script>
@endsection
