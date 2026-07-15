@extends('admin.layouts.admin-popup-layout')

@section('addStyle')
    <link type="text/css" rel="stylesheet" href="{{ asset('plugins/plupload/2.3.6/jquery.plupload.queue/css/jquery.plupload.queue.css') }}" />
@endsection

@section('contents')
    <div class="sub-contents">
        <div class="sub-tit-wrap">
            <h3 class="sub-tit">주소록 {{ empty($address->sid) ? '등록' : '수정' }}</h3>
        </div>

        <form id="address-frm" method="post" action="{{ route('mail.address.data') }}" data-sid="{{ $address->sid ?? 0 }}" data-case="address-{{ empty($address->sid) ? 'create' : 'update' }}">
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
                                <strong class="required">*</strong> 주소록 명칭
                            </th>
                            <td class="text-left">
                                <input type="text" name="title" id="title" value="{{ $address->title ?? '' }}" class="form-item">
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </fieldset>

            <div class="btn-wrap text-center">
                <a href="javascript:window.close();" class="btn btn-type1 color-type3">취소</a>
                <button type="submit" class="btn btn-type1 color-type6" id="submit">{{ empty($address->sid) ? '등록' : '수정' }}</button>
            </div>
        </form>
    </div>
@endsection

@section('addScript')
    <script>
        const form = '#address-frm';
        const dataUrl = $(form).attr('action');

        $(document).on('submit', form, function () {
            const title = $('input[name=title]');

            if (isEmpty(title.val())) {
                alert('주소록 명칭을 입력 해주세요.');
                title.focus();
                return false;
            }

            callAjax(dataUrl, formSerialize(form));
        });
    </script>
@endsection
