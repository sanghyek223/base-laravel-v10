@extends('admin.layouts.admin-popup-layout')

@section('addStyle')
@endsection

@section('contents')
    <div class="sub-contents">
        <div class="sub-tit-wrap">
            <h3 class="sub-tit">명단 개별 {{ empty($addressDetail->sid) ? '등록' : '수정' }}</h3>
        </div>

        <form id="individual-frm" method="post" action="{{ route('mail.address.data') }}" data-ma_sid="{{ request()->ma_sid }}" data-sid="{{ $addressDetail->sid ?? 0 }}" data-case="individual-{{ empty($addressDetail->sid) ? 'create' : 'update' }}">
            <fieldset>
                <div class="table-wrap">
                    <table class="cst-table">
                        <colgroup>
                            <col style="width: 20%;">
                            <col>
                        </colgroup>

                        <tbody>
                        <tr>
                            <th scope="row" class="text-left">
                                <strong class="required">*</strong> 이름
                            </th>
                            <td class="text-left">
                                <input type="text" name="name" id="name" value="{{ $addressDetail->name ?? '' }}" class="form-item">
                            </td>
                        </tr>

                        <tr>
                            <th scope="row" class="text-left">
                                <strong class="required">*</strong> 이메일
                            </th>
                            <td class="text-left">
                                <input type="text" name="email" id="email" value="{{ $addressDetail->email ?? '' }}" class="form-item">
                            </td>
                        </tr>

                        <tr>
                            <th scope="row" class="text-left">
                                <strong class="required">*</strong> 휴대전화
                            </th>
                            <td class="text-left">
                                <input type="text" name="mobile" id="mobile" value="{{ $addressDetail->mobile ?? '' }}" class="form-item" phoneHyphen>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </fieldset>

            <div class="btn-wrap text-center">
                <a href="javascript:window.close();" class="btn btn-type1 color-type6">취소</a>
                <button type="submit" class="btn btn-type1 color-type3" id="submit">{{ empty($addressDetail->sid) ? '등록' : '수정' }}</button>
            </div>
        </form>
    </div>
@endsection

@section('addScript')
    <script>
        const form = '#individual-frm';
        const dataUrl = $(form).attr('action');

        $(document).on('submit', form, function () {
            const name = $('input[name=name]');
            const email = $('input[name=email]');

            if (isEmpty(name.val())) {
                alert('이름을 입력 해주세요.');
                name.focus();
                return false;
            }

            if (isEmpty(email.val())) {
                alert('이메일을 입력 해주세요.');
                email.focus();
                return false;
            }

            if (!emailCheck(email.val())) {
                alert('올바른 이메일 형식이 아닙니다.');
                email.focus();
                return false;
            }

            let ajaxData = formSerialize(form);
            ajaxData.ma_sid = $(form).data('ma_sid');

            callAjax(dataUrl, ajaxData);
        });
    </script>
@endsection
