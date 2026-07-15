@extends('admin.layouts.admin-layout')

@section('addStyle')
@endsection

@section('contents')
    <div class="sub-contents">
        <form id="searchF" name="searchF" action="{{ route('mail.address') }}" class="sch-form-wrap">
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
                            <th scope="row">주소록 명</th>
                            <td class="text-left">
                                <input type="text" name="title" value="{{ request()->title ?? '' }}" class="form-item">
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="btn-wrap text-center">
                    <button type="submit" class="btn btn-type1 color-type17">검색</button>
                    <a href="{{ route('mail.address') }}" class="btn btn-type1 color-type18">검색 초기화</a>
                </div>
            </fieldset>
        </form>

        <div class="text-right">
            <a href="{{ route('mail.address.upsert') }}" class="btn btn-small btn-type1 color-type20 call-popup" data-name="address-upsert" data-width="900" data-height="350">
                주소록 등록
            </a>
        </div>

        <div class="table-wrap" style="margin-top: 10px;">
            <table class="cst-table list-table">
                <caption class="hide">목록</caption>

                <colgroup>
                    <col style="width:6%;">
                    <col>
                    <col style="width:10%;">
                    <col style="width:15%;">
                    <col style="width:7%;">
                </colgroup>

                <thead>
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">주소록명</th>
                    <th scope="col">인원</th>
                    <th scope="col">생성일</th>
                    <th scope="col">관리</th>
                </tr>
                </thead>

                <tbody>
                @forelse($list as $row)
                    <tr data-sid="{{ $row->sid }}">
                        <td>{{ $row->seq }}</td>
                        <td>
                            <a href="{{ route('mail.address.detail', ['ma_sid' => $row->sid]) }}">
                                <b>{{ $row->title }}</b>
                            </a>
                        </td>
                        <td>{{ number_format($row->list_count) }}</td>
                        <td>{{ $row->created_at }}</td>
                        <td>
                            <a href="{{ route('mail.address.upsert', ['sid' => $row->sid]) }}" class="btn-admin call-popup" data-name="address-upsert" data-width="900" data-height="350">
                                <img src="/assets_admin/image/ic_modify.png" alt="수정">
                            </a>

                            <a href="javascript:void(0);" class="btn-admin btn-del">
                                <img src="/assets_admin/image/ic_del.png" alt="삭제">
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">등록된 주소록이 없습니다.</td>
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
        const dataUrl = '{{ route('mail.address.data') }}';

        const getPK = (_this) => {
            return $(_this).closest('tr').data('sid');
        }

        $(document).on('click', '.btn-del', function() {
            const ajaxData = {
                'sid': getPK(this),
                'case': 'address-delete',
            };

            if (confirm('삭제 하시겠습니까?')) {
                callAjax(dataUrl, ajaxData);
            }
        });
    </script>
@endsection
