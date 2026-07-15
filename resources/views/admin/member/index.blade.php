@extends('admin.layouts.admin-layout')

@section('addStyle')
@endsection

@section('contents')
    <div class="sub-contents">
        <div class="sub-tab-wrap">
            <ul class="sub-tab-menu cf">
                <li class="{{ empty($memberCase) ? 'on' : '' }}">
                    <a href="{{ route('member') }}">전체 회원</a>
                </li>

                <li class="{{ request()->case == 'withdrawal' ? 'on' : '' }}">
                    <a href="{{ route('member', ['case' => 'withdrawal']) }}">탈퇴 회원</a>
                </li>
            </ul>
        </div>

        <form id="searchF" name="searchF" class="sch-form-wrap">
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
                            <th scope="row">활동 상태</th>
                            <td class="text-left">
                                <div class="radio-wrap">
                                    <div class="radio-group">
                                        <input type="radio" name="active" id="active_all"  value="all" {{ request()->input('active', 'all') === 'all' ? 'checked' : '' }}>
                                        <label for="active_all">전체</label>
                                    </div>

                                    @foreach($userConfig['active'] as $key => $val)
                                        <div class="radio-group">
                                            <input type="radio" name="active" id="active_{{ $key }}"  value="{{ $key }}" {{ request()->input('active', '') === $key ? 'checked' : '' }}>
                                            <label for="active_{{ $key }}">{{ $val }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </td>

                            <th scope="row">회원 등급</th>
                            <td class="text-left">
                                <div class="checkbox-wrap">
                                    @foreach($userConfig['level'] as $key => $val)
                                        <div class="checkbox-group">
                                            <input type="checkbox" name="level[]" id="level_{{ $key }}"  value="{{ $key }}" {{ in_array($key, request()->input('level', [])) ? 'checked' : '' }}>
                                            <label for="level_{{ $key }}">{{ $val }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">상세검색</th>
                            <td class="text-left" colspan="3">
                                <select name="search_key" class="form-item mr-5" style="width: 20%;">
                                    <option value="">선택</option>

                                    @foreach($userConfig['admin_search'] as $key => $val)
                                        <option value="{{ $key }}" {{ request()->input('search_key', '') === $key ? 'selected' : '' }}>{{ $val }}</option>
                                    @endforeach
                                </select>

                                <input type="text" name="keyword" value="{{ request()->input('keyword', '') }}" class="form-item" style="width: 79%;">
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="btn-wrap text-center">
                    <button type="submit" class="btn btn-type1 color-type17">검색</button>
                    <a href="{{ route('member', $memberCase) }}" class="btn btn-type1 color-type18">검색 초기화</a>
                    <a href="{{ route('member.excel', request()->except(['page']) + $memberCase) }}" class="btn btn-type1 color-type19">Get Excel File</a>
                </div>
            </fieldset>
        </form>

        @switch($memberCase['case'] ?? '')
            @case('withdrawal')
                @include('admin.member.include.withdrawal-list')
                @break

            @default
                @include('admin.member.include.default-list')
                @break
        @endswitch


        {{ $list->links('pagination::custom') }}
    </div>
@endsection

@section('addScript')
    <script>
        const dataUrl = '{{ route('member.data') }}';

        const getPK = (_this) => {
            return $(_this).closest('tr').data('sid');
        }
    </script>

    @yield('list-script')
@endsection
