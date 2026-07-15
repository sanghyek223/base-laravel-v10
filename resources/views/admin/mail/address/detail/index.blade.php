@extends('admin.layouts.admin-layout')

@section('addStyle')
@endsection

@section('contents')
    <div class="sub-contents">
        <div class="sub-tit-wrap mb-10">
            <h3 class="sub-tit">[ {{ $address->title }} ] 주소록</h3>
        </div>

        <form id="searchF" name="searchF" action="{{ route('mail.address.detail', ['ma_sid' => $address->sid]) }}" class="sch-form-wrap">
            <fieldset>
                <legend class="hide">검색</legend>
                <div class="table-wrap">
                    <table class="cst-table">
                        <colgroup>
                            <col style="width: 20%;">
                            <col>
                        </colgroup>

                        <tbody>
                        <tr>
                            <th scope="row">상세검색</th>
                            <td class="text-left">
                                <div style="display: flex">
                                    <select name="field" class="form-item" style="width: 15%; margin-right: 10px;">
                                        <option value="">선택 해주세요.</option>
                                        <option value="name" {{ (request()->keyfield ?? '') === 'name' ? 'selected' : '' }}>이름</option>
                                        <option value="email" {{ (request()->keyfield ?? '') === 'email' ? 'selected' : '' }}>이메일</option>
                                        <option value="office" {{ (request()->keyfield ?? '') === 'office' ? 'selected' : '' }}>근무처명</option>
                                    </select>

                                    <input type="text" name="keyword" value="{{ request()->keyword ?? '' }}" class="form-item">
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="btn-wrap text-center">
                    <button type="submit" class="btn btn-type1 color-type17">검색</button>
                    <a href="{{ route('mail.address', ['ma_sid' => $address->sid]) }}" class="btn btn-type1 color-type18">검색 초기화</a>
                </div>
            </fieldset>
        </form>

        <div class="text-right">
            <a href="{{ route('mail.address.detail.upsert', ['ma_sid' => $address->sid, 'type' => 'individual']) }}" class="btn btn-small btn-type1 color-type8 call-popup" data-name="address-upsert-individual" data-width="850" data-height="500">개별 등록</a>
            <a href="{{ route('mail.address.detail.upsert', ['ma_sid' => $address->sid, 'type' => 'collective']) }}" class="btn btn-small btn-type1 color-type10 call-popup" data-name="address-upsert-collective" data-width="1050" data-height="700">일괄 등록</a>
            <a href="{{ route('mail.address') }}" class="btn btn-small btn-type1 color-type20">목록 으로</a>
        </div>

        <div class="table-wrap" style="margin-top: 10px;">
            <table class="cst-table list-table">
                <caption class="hide">목록</caption>

                <colgroup>
                    <col style="width:5%;">
                    <col style="width:10%;">
                    <col>
                    <col style="width:15%;">
                    <col style="width:10%;">
                    <col style="width:5%;">
                </colgroup>

                <thead>
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">이름</th>
                    <th scope="col">이메일</th>
                    <th scope="col">휴대전화</th>
                    <th scope="col">등록일</th>
                    <th scope="col">관리</th>
                </tr>
                </thead>

                <tbody>
                @forelse($list as $row)
                    <tr data-sid="{{ $row->sid }}">
                        <td>{{ $row->seq }}</td>
                        <td>{{ $row->name }}</td>
                        <td>{{ $row->email }}</td>
                        <td>{{ $row->mobile }}</td>
                        <td>{{ $row->created_at }}</td>
                        <td>
                            <a href="{{ route('mail.address.detail.upsert', ['ma_sid' => $address->sid, 'type' => 'individual', 'sid' => $row->sid]) }}" class="btn-admin call-popup" data-name="address-upsert-individual" data-width="850" data-height="500">
                                <img src="/assets_admin/image/ic_modify.png" alt="수정">
                            </a>

                            <a href="javascript:void(0);" class="btn-admin btn-del">
                                <img src="/assets_admin/image/ic_del.png" alt="삭제">
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">등록된 명단이 없습니다.</td>
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
                'case': 'addressDetail-delete',
            };

            if (confirm('삭제 하시겠습니까?')) {
                callAjax(dataUrl, ajaxData);
            }
        });
    </script>
@endsection
