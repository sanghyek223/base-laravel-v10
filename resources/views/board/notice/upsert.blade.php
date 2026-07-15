@extends('layouts.web-layout')

@section('addStyle')
    <link href="/html/bbs/general/assets/css/board.css" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="{{ asset('plugins/plupload/2.3.6/jquery.plupload.queue/css/jquery.plupload.queue.css') }}" />

    <style>
        .eDate-display {
            display: {{ ($board->date_type ?? '') == 'L' ? 'inline-block' : 'none' }}
        }
    </style>
@endsection

@section('contents')
    <article class="sub-contents">
        <div class="sub-conbox inner-layer">
            <!-- //s:board -->
            @include('layouts.include.sub-tit-wrap')

            <div id="board" class="board-wrap">
                <div class="board-write">
                    <div class="write-form-wrap">
                        <form id="board-frm" method="post" data-sid="{{ $board->sid ?? 0 }}" data-case="board-{{ empty($board->sid) ? 'create' : 'update' }}">
                            <fieldset>
                                <legend class="hide">글쓰기</legend>

                                <div class="write-contop text-right">
                                    <div class="help-text"><strong class="required">*</strong> 표시는 필수입력 항목입니다.</div>
                                </div>

                                <ul class="write-wrap">
                                    @if($boardConfig['use']['writer'])
                                        <li>
                                            <div class="form-tit"><strong class="required">*</strong> 작성자</div>
                                            <div class="form-con">
                                                <input type="text" name="writer" id="writer" class="form-item" value="{{ $board->name_kr ?? thisUser()->name_kr }}">
                                            </div>
                                        </li>
                                    @endif

                                    @if($boardConfig['use']['email'])
                                        <li>
                                            <div class="form-tit"><strong class="required">*</strong> 이메일</div>
                                            <div class="form-con">
                                                <input type="text" name="email" id="email" class="form-item" value="{{ $board->email ?? thisUser()->email }}">
                                            </div>
                                        </li>
                                    @endif

                                    @if($boardConfig['use']['subject'])
                                        <li>
                                            <div class="form-tit"><strong class="required">*</strong> {{ $boardConfig['subject'] }}</div>
                                            <div class="form-con">
                                                <input type="text" name="subject" id="subject" class="form-item" value="{{ $board->subject ?? '' }}">

                                                @if($boardConfig['use']['notice'] || $boardConfig['use']['main'])
                                                    <div class="checkbox-wrap cst mt-10">
                                                        @if($boardConfig['use']['notice'])
                                                            <label for="notice" class="checkbox-group">
                                                                <input type="checkbox" name="notice" id="notice" value="Y" {{ ($board->notice ?? 'N') == 'Y' ? 'checked' : '' }}>공지
                                                            </label>
                                                        @endif

                                                        @if($boardConfig['use']['main'])
                                                            <label for="main" class="checkbox-group">
                                                                <input type="checkbox" name="main" id="main" value="Y" {{ ($board->main ?? 'N') == 'Y' ? 'checked' : '' }}>PUSH
                                                            </label>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </li>
                                    @endif

                                    @if($boardConfig['use']['hide'])
                                        <li>
                                            <div class="form-tit"><strong class="required">*</strong> 노출 여부</div>
                                            <div class="form-con">
                                                <div class="radio-wrap cst">
                                                    @foreach($boardConfig['options']['hide'] as $key => $val)
                                                        <label for="hide_{{ $key }}" class="radio-group">
                                                            <input type="radio" name="hide" id="hide_{{ $key }}" value="{{ $key }}" {{ (($board->hide ?? '') == $key) ? 'checked' : '' }}>
                                                            {{ $val }}
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </li>
                                    @endif

                                    @if($boardConfig['use']['popup'])
                                        @php
                                            $popupDisplay = (($board->popup ?? 'N') === 'Y') ? '' : 'none';
                                            $popupDetailDisplay = (($popup->popup_detail ?? 'N') === 'Y') ? '' : 'none';
                                            $popupContentDisplay = (($popup->popup_select ?? '1') == '2') ? '' : 'none';
                                        @endphp

                                        <li>
                                            <div class="form-tit"><strong class="required">*</strong> 팝업 설정</div>
                                            <div class="form-con">
                                                <div class="radio-wrap cst">
                                                    @foreach($boardConfig['options']['popup_yn'] as $key => $val)
                                                        <label for="popup_{{ $key }}" class="radio-group">
                                                            <input type="radio" name="popup" id="popup_{{ $key }}" value="{{ $key }}" {{ (($board->popup ?? 'N') == $key) ? 'checked' : '' }}>
                                                            {{ $val }}
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </li>

                                        <li class="popupBox" style="display: {{ $popupDisplay }}">
                                            <div class="form-tit">팝업 템플릿</div>
                                            <div class="form-con">
                                                <div class="radio-wrap cst">
                                                    @foreach($boardConfig['options']['popup_skin'] as $key => $val)
                                                        <label for="popup_skin_{{ $key }}" class="radio-group">
                                                            <input type="radio" name="popup_skin" id="popup_skin_{{ $key }}" value="{{ $key }}" {{ (($popup->popup_skin ?? '0') == $key) ? 'checked' : '' }}>
                                                            {{ $val }}
                                                        </label>
                                                    @endforeach

                                                    <a href="javascript:void(0);" class="btn btn-small color-type1" id="popup_preview">미리보기</a>
                                                </div>
                                            </div>
                                        </li>

                                        <li class="popupBox" style="display: {{ $popupDisplay }}">
                                            <div class="form-tit">팝업 내용 선택</div>
                                            <div class="form-con">
                                                <div class="radio-wrap cst">
                                                    @foreach($boardConfig['options']['popup_contents'] as $key => $val)
                                                        <label for="popup_select_{{ $key }}" class="radio-group">
                                                            <input type="radio" name="popup_select" id="popup_select_{{ $key }}" value="{{ $key }}" {{ (($popup->popup_select ?? '1') == $key) ? 'checked' : '' }}>
                                                            {{ $val }}
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </li>

                                        <li class="popupBox" style="display: {{ $popupDisplay }}">
                                            <div class="form-tit">팝업 자세히 보기</div>
                                            <div class="form-con">
                                                <div class="radio-wrap cst">
                                                    @foreach($boardConfig['options']['popup_detail'] as $key => $val)
                                                        <label for="popup_detail_{{ $key }}" class="radio-group">
                                                            <input type="radio" name="popup_detail" id="popup_detail_{{ $key }}" value="{{ $key }}" {{ (($popup->popup_detail ?? 'N') == $key) ? 'checked' : '' }}>
                                                            {{ $val }}
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </li>

                                        <li class="popupDetailBox" style="display: {{ $popupDetailDisplay }}">
                                            <div class="form-tit">팝업 자세히 보기 LINK</div>
                                            <div class="form-con">
                                                <input type="text" name="popup_link" id="popup_link" class="form-item" value="{{ $popup->popup_link ?? '' }}">
                                            </div>
                                        </li>

                                        @if($boardConfig['popup_style'] === 'layer')
                                            <li class="popupBox" style="display: {{ $popupDisplay }}">
                                                <div class="form-tit">팝업 사이즈</div>
                                                <div class="form-con">
                                                    <div class="form-group">
                                                        <span class="text">사이즈</span> :
                                                        <input type="text" name="width" id="width" class="form-item w-10p" value="{{ $popup->width ?? '500' }}" maxlength="4" onlyNumber> X
                                                        <input type="text" name="height" id="height" class="form-item w-10p" value="{{ $popup->height ?? '600' }}" maxlength="4" onlyNumber>
                                                    </div>

                                                    <div class="form-group mt-10">
                                                        <span class="text">위치</span> :
                                                        위에서 <input type="text" name="position_y" id="position_y" class="form-item w-10p" value="{{ $popup->position_y ?? '0' }}" maxlength="4" onlyNumber> px,
                                                        왼쪽에서 <input type="text" name="position_x" id="position_x" class="form-item w-10p" value="{{ $popup->position_x ?? '0' }}" maxlength="4" onlyNumber> px
                                                    </div>
                                                </div>
                                            </li>
                                        @endif

                                        <li class="popupBox" style="display: {{ $popupDisplay }}">
                                            <div class="form-tit">팝업 시작일 / 종료일</div>
                                            <div class="form-con">
                                                <div class="form-group">
                                                    <span class="text">시작일 : </span> <input type="text" name="popup_sDate" id="popup_sDate" class="form-item w-20p" value="{{ $popup->popup_sDate ?? '' }}" readonly datepicker> &nbsp;
                                                    <span class="text">종료일 : </span> <input type="text" name="popup_eDate" id="popup_eDate" class="form-item w-20p" value="{{ $popup->popup_eDate ?? '' }}" readonly datepicker>
                                                </div>
                                            </div>
                                        </li>

                                        <li class="popupContentBox" style="display: {{ $popupContentDisplay }}">
                                            <div class="form-con">
                                                <textarea name="popup_contents" id="popup_contents" class="tinymce">{{ $popup->popup_contents ?? '' }}</textarea>
                                            </div>
                                        </li>
                                    @endif

                                    @if($boardConfig['use']['gubun'])
                                        <li>
                                            <div class="form-tit">{{ $boardConfig['gubun']['name'] }}</div>

                                            <div class="form-con">
                                                @switch($boardConfig['gubun']['type'])
                                                    @case('radio')
                                                        <div class="radio-wrap cst">
                                                            @foreach($boardConfig['gubun']['item'] as $key => $val)
                                                                <label for="gubun_{{ $key }}" class="radio-group">
                                                                    <input type="radio" name="gubun" id="gubun_{{ $key }}" value="{{ $key }}" {{ (($board->gubun ?? '') == $key) ? 'checked' : '' }}>
                                                                    {{ $val }}
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                        @break

                                                    @case('select')
                                                        <select name="gubun" id="gubun" class="form-item">
                                                            <option value="">선택</option>
                                                            @foreach($boardConfig['gubun']['item'] as $key => $val)
                                                                <option value="{{ $key }}" {{ (($board->gubun ?? '') == $key) ? 'selected' : '' }}>{{ $val }}</option>
                                                            @endforeach
                                                        </select>
                                                        @break
                                                @endswitch
                                            </div>
                                        </li>
                                    @endif

                                    @if($boardConfig['use']['category'])
                                        <li>
                                            <div class="form-tit">{{ $boardConfig['category']['name'] }}</div>

                                            <div class="form-con">
                                                @switch($boardConfig['category']['type'])
                                                    @case('radio')
                                                        <div class="radio-wrap cst">
                                                            @foreach($boardConfig['category']['item'] as $key => $val)
                                                                <label for="category_{{ $key }}" class="radio-group">
                                                                    <input type="radio" name="category" id="category_{{ $key }}" value="{{ $key }}" {{ (($board->category ?? '') == $key) ? 'checked' : '' }}>
                                                                    {{ $val }}
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                        @break

                                                    @case('select')
                                                        <select name="category" id="category" class="form-item">
                                                            <option value="">선택</option>
                                                            @foreach($boardConfig['category']['item'] as $key => $val)
                                                                <option value="{{ $key }}" {{ (($board->category ?? '') == $key) ? 'selected' : '' }}>{{ $val }}</option>
                                                            @endforeach
                                                        </select>
                                                        @break
                                                @endswitch
                                            </div>
                                        </li>
                                    @endif

                                    @if($boardConfig['use']['date'])
                                        <li>
                                            <div class="form-tit"><strong class="required">*</strong> 행사 기간</div>
                                            <div class="form-con">
                                                <div class="radio-wrap cst">
                                                    @foreach($boardConfig['options']['date_type'] as $key => $val)
                                                        <div class="radio-group">
                                                            <input type="radio" name="date_type" id="date_type_{{ $key }}" value="{{ $key }}" {{ ($board->date_type ?? '') == $key ? 'checked' : '' }}>
                                                            <label for="date_type_{{ $key }}">{{ $val }}</label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </li>

                                        <li>
                                            <div class="form-tit"><strong class="required">*</strong> {{ $boardConfig['date']['name'] }}</div>
                                            <div class="form-con">
                                                <div class="form-group">
                                                    <input type="text" name="event_sDate" id="event_sDate" class="form-item w-20p flatpickr flatpickr-input" value="{{ $board->event_sDate ?? '' }}" readonly datepicker>
                                                    <input type="time" name="event_sTime" id="event_sTime" class="form-item w-20p flatpickr flatpickr-input" value="{{ $board->event_sTime ?? '' }}">
                                                    <span class="eDate-display">~</span>
                                                    <input type="text" name="event_eDate" id="event_eDate" class="form-item w-20p flatpickr flatpickr-input eDate-display" value="{{ $board->event_eDate ?? '' }}" readonly datepicker>
                                                    <input type="time" name="event_eTime" id="event_eTime" class="form-item w-20p flatpickr flatpickr-input eDate-display" value="{{ $board->event_eTime ?? '' }}">
                                                </div>
                                            </div>
                                        </li>
                                    @endif

                                    @if($boardConfig['use']['place'])
                                        <li>
                                            <div class="form-tit">장소</div>

                                            <div class="form-con">
                                                <input type="text" name="place" id="place" class="form-item" value="{{ $board->place ?? '' }}">
                                            </div>
                                        </li>
                                    @endif

                                    @if($boardConfig['use']['link'])
                                        <li>
                                            <div class="form-tit">LINK URL</div>
                                            <div class="form-con">
                                                <input type="text" name="link_url" id="link_url" class="form-item" value="{{ $board->link_url ?? 'https://' }}" noneSpace>
                                            </div>
                                        </li>
                                    @endif

                                    @if($boardConfig['use']['file'])
                                        @foreach($boardConfig['file'] as $key => $val)
                                            <li>
                                                <div class="form-tit">{{ $val['name'] }}</div>
                                                <div class="form-con">
                                                    <div class="filebox">
                                                        <input class="upload-name form-item" id="file{{ $key }}_name" placeholder="파일첨부" readonly="readonly">
                                                        <label for="file{{ $key }}">파일찾기</label>
                                                        <input type="file" name="file{{ $key }}" id="file{{ $key }}" class="file-upload">

                                                        @if (!empty($board->{"realfile{$key}"}))
                                                            <div class="attach-file">
                                                                <a href="{{ $board->downloadUrl($key) }}" class="link">
                                                                    {{ $board->{"filename{$key}"} }}
                                                                </a>

                                                                <a href="javascript:void(0);" class="btn-file-delete text-red">X</a>
                                                            </div>

                                                            <input type="hidden" name="file{{ $key }}_del" id="file{{ $key }}_del">
                                                        @endif
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    @endif

                                    @if($boardConfig['use']['thumbnail'])
                                        <li>
                                            <div class="form-tit">{{ $boardConfig['thumbnail']['name'] }}</div>
                                            <div class="form-con">
                                                <div class="filebox">
                                                    <input class="upload-name form-item" id="thumbnail_name" placeholder="파일첨부" readonly="readonly">
                                                    <label for="thumbnail">파일찾기</label>
                                                    <input type="file" name="thumbnail" id="thumbnail" class="file-upload" accept=".jpg, .png, .gif" data-accept="jpg|png|gif">

                                                    @if (!empty($board->thumbnail_filename))
                                                        <div class="attach-file">
                                                            <a href="{{ $board->downloadUrl('thumbnail') }}" class="link">
                                                                {{ $board->thumbnail_filename }}
                                                            </a>

                                                            <a href="javascript:void(0);" class="btn-file-delete text-red">X</a>
                                                        </div>

                                                        <input type="hidden" name="thumbnail_del" id="thumbnail_del">
                                                    @endif
                                                </div>
                                            </div>
                                        </li>
                                    @endif

                                    @if($boardConfig['use']['plupload'] && ($board->files_count ?? 0) > 0)
                                        <li>
                                            <div class="form-tit">첨부파일</div>
                                            <div class="form-con">
                                                <div class="checkbox-wrap cst">
                                                    @foreach($board->files as $key => $file)
                                                        <div class="checkbox-group" style="width: 100%;">
                                                            <input type="checkbox" name="plupload_file_del[]" id="plupload_file_del{{ $key }}" value="{{ $file->sid }}">
                                                            <label for="plupload_file_del{{ $key }}" style="margin-left: 0.3rem; margin-right: 0.5rem;"> <span style="color: red;"> 삭제</span> - </label>

                                                            <a href="{{ $file->downloadUrl() }}">
                                                                {{ $file->filename }}
                                                            </a>

                                                            <span style="margin-left: 0.3rem;">(다운 : {{ number_format($file->download) }})</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </li>
                                    @endif

                                    @if($boardConfig['use']['contents'])
                                        <li>
                                            <div class="form-con">
                                                <textarea name="contents" id="contents" class="tinymce">{{ $board->contents ?? '' }}</textarea>
                                            </div>
                                        </li>
                                    @endif

                                    @if($boardConfig['use']['plupload'])
                                        <li>
                                            <div class="form-con">
                                                <div id="plupload"></div>
                                            </div>
                                        </li>
                                    @endif
                                </ul>

                                <div class="btn-wrap text-center">
                                    <a href="javascript:void(0);" class="btn btn-board btn-cancel">취소</a>

                                    <button type="submit" class="btn btn btn-board btn-write">
                                        {{ empty($board->sid) ? '등록' : '수정' }}
                                    </button>

                                    <a href="{{ route('board', ['code' => $code]) }}" class="btn btn-board btn-list full-right">목록</a>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
            <!-- //e:board -->
        </div>
    </article>
@endsection

@section('addScript')
    @include("board.default-script")

    <script>
        // 게시글 작성 취소
        $(document).on('click', '.btn-cancel', function(e) {
            e.preventDefault();

            const msg = ($(boardForm).data('sid') == 0) ?
                '등록을 취소하시겠습니까?' :
                '수정을 취소하시겠습니까?';

            if (confirm(msg)) {
                location.replace('{{ route('board', ['code' => $code]) }}');
            }
        });

        // 기간설정 사용시
        if(boardUseConfig.date) {
            $(document).on('change', 'input:radio[name=date_type]', function() {
                const target = $('.eDate-display');

                if ($(this).val() === "L") {
                    target.show();
                } else {
                    target.hide();
                    target.find('input').val('');
                }
            });
        }

        // 첨부파일 (plupload) 사용시
        if(boardUseConfig.plupload) {
            pluploadInit({
                multipart_params: {
                    directory: '{{ $boardConfig['directory'] }}',
                },
                filters: {
                    max_file_size: '20mb'
                },
            });
        }

        // 첨부파일 (단일파일) or 썸네일 사용시
        if(boardUseConfig.file || boardUseConfig.thumbnail) {
            $(document).on('click', 'input[type=file]', function (e) {
                const target = $(this).closest('.filebox').find('.attach-file');

                if (!fileDelCheck(target)) {
                    e.preventDefault();
                }
            });

            $(document).on('change', 'input[type=file]', function () {
                const name = $(this).attr('name');
                fileCheck(this, `#${name}_name`);
            });

            $(document).on('click', '.btn-file-delete', function () {
                const name = $(this).closest('.filebox').find('input[type=file]').attr('name');
                const target = $(this).closest('.filebox').find('.attach-file');

                target.remove();
                $(`#${name}_del`).val('Y');
            });
        }

        // 팝업 사용시
        if(boardUseConfig.popup) {
            // 팝업 설정 radio
            $(document).on('click', 'input:radio[name=popup]', function() {
                if ($(this).val() === "Y") {
                    $(".popupBox").show();
                } else {
                    $(".popupBox").hide();
                    $(".popupBox").find("input:text").val('');
                    tinymce.get('popup_contents').getContent('');
                }
            });

            // 팝업 내용 선택
            $(document).on('click', 'input:radio[name=popup_select]', function() {
                $('.popupContentBox').css('display', $(this).val() == '2' ? 'table-row' : 'none');
            });

            // 팝업 자세히 보기 radio
            $(document).on('click', 'input:radio[name=popup_detail]', function() {
                if ($(this).val() === "Y") {
                    $(".popupDetailBox").show();
                } else {
                    $(".popupDetailBox").hide();
                    $(".popupDetailBox").find("input:text").val('');
                }
            });

            // 팝업 미리보기
            $(document).on('click', '#popup_preview', function(e) {
                const subject = $("#subject").val();

                if (isEmpty(subject)) {
                    alert('제목을 입력해주세요.');
                    $('#subject').focus();
                    return;
                }

                if (!$('input[name=popup_skin]').is(':checked')) {
                    alert('팝업 템플릿을 선택해주세요.');
                    $('input[name=popup_skin]').focus();
                    return;
                }

                @if($boardConfig['popup_style'] === 'layer')
                    if (parseInt($("#width").val()) < popupMinWidth) {
                        alert(`${popupMinWidth} 이상 입력해주세요.`);
                        $('#width').focus();
                        return;
                    }

                    if (parseInt($("#height").val()) < popupMinHeight) {
                        alert(`${popupMinHeight} 이상 입력해주세요.`);
                        $('#height').focus();
                        return;
                    }
                @endif

                const contents = ($('input:radio[name=popup_select]:checked').val() == "1")
                    ? 'contents'
                    : 'popup_contents';

                const tinyVal = tinymce.get(contents).getContent();
                // let tinyValChk = tinyVal.replace(/<[^>]*>?/g, ''); // html 태그 삭제
                const tinyValChk = tinyVal.replace(/\&nbsp;/g, ' '); // &nbsp 삭제;

                if (isEmpty(tinyValChk)) {
                    alert('내용을 입력해주세요.');
                    $('#' + contents).focus();
                    return;
                }

                let ajaxData = newFormData(boardForm);
                ajaxData.append('case', 'popup-{{ $boardConfig['popup_style'] }}-preview');
                ajaxData.append('contents', tinymce.get('contents').getContent());
                ajaxData.append('popup_contents', tinymce.get('popup_contents').getContent());

                const plupload_queue = $('#plupload').pluploadQueue();

                $(plupload_queue.files).each(function (k, v) {
                    ajaxData.append('plupload[]', v.name);
                });

                callMultiAjax(dataUrl, ajaxData);
            });

            // 팝업 미리보기 닫기
            $(document).on('click', '.btn-pop-close, .btn-pop-today-close', function () {
                $(this).closest('.popup-wrap').remove();
            });
        }

        $(document).on('submit', boardForm, function () {
            if (boardUseConfig.writer) {
                const writer = $('#writer');
                if (isEmpty(writer.val())) {
                    alert(`작성자를 입력해주세요.`);
                    writer.focus();
                    return false;
                }
            }

            if (boardUseConfig.email) {
                const email = $('#email');
                if (isEmpty(email.val())) {
                    alert(`이메일을 입력해주세요.`);
                    email.focus();
                    return false;
                }

                if (!emailCheck(email.val())) {
                    alert(`올바른 이메일형식이 아닙니다.`);
                    email.focus();
                    return false;
                }
            }

            if (boardUseConfig.subject) {
                const subject = $('#subject');
                if (isEmpty(subject.val())) {
                    alert(`{{ $boardConfig['subject'] }}을 입력해주세요.`);
                    subject.focus();
                    return false;
                }
            }

            if (boardUseConfig.hide) {
                const hide = $('input[name=hide]');
                if (!hide.is(':checked')) {
                    alert('공개여부를 선택해주세요.');
                    hide.focus();
                    return false;
                }
            }

            if (boardUseConfig.gubun) {
                switch ('{{ $boardConfig['gubun']['type'] }}') {
                    case 'radio':
                        if (!$('input[name=gubun]').is(':checked')) {
                            alert(`{{ $boardConfig['gubun']['name'] }}를 선택해주세요.`);
                            $('input[name=gubun]').focus();
                            return false;
                        }
                        break;

                    case 'select':
                        if (isEmpty($('select[name=gubun]').val())) {
                            alert(`{{ $boardConfig['gubun']['name'] }}를 선택해주세요.`);
                            $('select[name=gubun]').focus();
                            return false;
                        }
                        break;
                }
            }

            if (boardUseConfig.category) {
                switch ('{{ $boardConfig['category']['type'] }}') {
                    case 'radio':
                        if (!$('input[name=category]').is(':checked')) {
                            alert(`{{ $boardConfig['category']['name'] }}를 선택해주세요.`);
                            $('input[name=category]').focus();
                            return false;
                        }
                        break;

                    case 'select':
                        if (isEmpty($('select[name=category]').val())) {
                            alert(`{{ $boardConfig['category']['name'] }}를 선택해주세요.`);
                            $('select[name=category]').focus();
                            return false;
                        }
                        break;
                }
            }

            if (boardUseConfig.date) {
                const date_type = $('input[name=date_type]');
                if (!date_type.is(':checked')) {
                    alert('행사 기간을 선택해주세요.');
                    date_type.focus();
                    return false;
                }

                const event_sDate = $('input[name=event_sDate]');
                if (isEmpty(event_sDate.val())) {
                    alert('행사 시작일을 선택해주세요.');
                    event_sDate.focus();
                    return false;
                }

                // const event_sTime = $('input[name=event_sTime]');
                // if (isEmpty(event_sTime.val())) {
                //     alert('행사 시작시간을 선택해주세요.');
                //     event_sTime.focus();
                //     return false;
                // }

                if ($('input[name=date_type]:checked').val() == 'L') {
                    const event_eDate = $('input[name=event_eDate]');
                    if (isEmpty(event_eDate.val())) {
                        alert('행사 종료일을 선택해주세요.');
                        event_eDate.focus();
                        return false;
                    }

                    // const event_eTime = $('input[name=event_eTime]');
                    // if (isEmpty(event_eTime.val())) {
                    //     alert('행사 종료시간을 선택해주세요.');
                    //     event_eTime.focus();
                    //     return false;
                    // }
                }
            }

            if (boardUseConfig.popup) {
                const popup = $('input[name=popup]');
                if (!popup.is(':checked')) {
                    alert('팝업설정를 선택해주세요.');
                    popup.focus();
                    return false;
                }

                // 팝업 사용 선택시
                if($('input[name=popup]:checked').val() == 'Y') {
                    const popup_skin = $('input[name=popup_skin]');
                    if (!popup_skin.is(':checked')) {
                        alert('팝업 탬플릿을 선택해주세요.');
                        popup_skin.focus();
                        return false;
                    }

                    const popup_select = $('input[name=popup_select]');
                    if (!popup_select.is(':checked')) {
                        alert('팝업 내용을 선택해주세요.');
                        popup_select.focus();
                        return false;
                    }

                    const popup_detail = $('input[name=popup_detail]');
                    if (!popup_detail.is(':checked')) {
                        alert('팝업 자세히 보기를 선택해주세요.');
                        popup_detail.focus();
                        return false;
                    }

                    if ($('input[name=popup_detail]:checked').val() == 'Y') {
                        const popup_link = $('input[name=popup_link]');
                        if (isEmpty(popup_link.val())) {
                            alert('팝업 자세히 보기 LINK 를 입력해주세요.');
                            popup_link.focus();
                            return false;
                        }
                    }

                    @if($boardConfig['popup_style'] === 'layer' /* layer 팝업 사용시 */)

                        const width = $('input[name=width]');
                        if (isEmpty(width.val())) {
                            alert('팝업 가로 사이즈를 입력해주세요.');
                            width.focus();
                            return false;
                        }

                        if (popupMinWidth > parseInt(width.val())) {
                            alert(`${popupMinHeight} 이상 입력해주세요.`);
                            width.focus();
                            return false;
                        }

                        const height = $('input[name=height]');
                        if (isEmpty(height.val())) {
                            alert('팝업 세로 사이즈를 입력해주세요.');
                            height.focus();
                            return false;
                        }

                        if (popupMinHeight > parseInt(height.val())) {
                            alert(`${popupMinHeight} 이상 입력해주세요.`);
                            height.focus();
                            return false;
                        }

                        const position_x = $('input[name=position_x]');
                        if (isEmpty(position_x.val())) {
                            alert('팝업 위에서 위치를 입력해주세요.');
                            position_x.focus();
                            return false;
                        }

                        const position_y = $('input[name=position_y]');
                        if (isEmpty(position_y.val())) {
                            alert('팝업 왼쪽에서 위치를 입력해주세요.');
                            position_y.focus();
                            return false;
                        }

                    @endif

                    const popup_sDate = $('input[name=popup_sDate]');
                    if (isEmpty(popup_sDate.val())) {
                        alert('팝업 시작일을 선택해주세요.');
                        popup_sDate.focus();
                        return false;
                    }

                    const popup_eDate = $('input[name=popup_eDate]');
                    if (isEmpty(popup_eDate.val())) {
                        alert('팝업 종료일을 선택해주세요.');
                        popup_eDate.focus();
                        return false;
                    }

                    if ($('input[name=popup_select]:checked').val() == '2') {
                        let popupTinyVal = tinymce.get('popup_contents').getContent(); // 내용 가져오기
                        // tinyVal = tinyVal.replace(/<[^>]*>?/g, ''); // html 태그 삭제
                        popupTinyVal = popupTinyVal.replace(/\&nbsp;/g, ' '); // &nbsp 삭제

                        if (isEmpty(popupTinyVal)) {
                            alert('팝업 내용을 입력해주세요.');
                            return false;
                        }
                    }
                }
                /* END 팝업사용선택 */
            }

            let tinyVal = tinymce.get('contents').getContent(); // 내용 가져오기
            // tinyVal = tinyVal.replace(/<[^>]*>?/g, ''); // html 태그 삭제
            tinyVal = tinyVal.replace(/\&nbsp;/g, ' '); // &nbsp 삭제

            if (isEmpty(tinyVal)) {
                alert('내용을 입력해주세요.');
                return false;
            }

            if(boardUseConfig.plupload) { // plupload 사용할때
                const plupload_queue = $('#plupload').pluploadQueue();

                let fileCnt = plupload_queue.files.length;
                fileCnt = (fileCnt - previousUploadedFilesCount);

                if (fileCnt > 0) {
                    spinnerShow();
                    plupload_queue.start();
                    plupload_queue.bind('UploadComplete', function(up, files) {
                        spinnerHide();

                        if (plupload_queue.total.failed !== 0) {
                            alert('파일 업로드 실패');
                            location.reload();
                            return false;
                        }

                        // 업로드된 파일 수를 저장
                        previousUploadedFilesCount = up.files.length;
                        boardSubmit();
                    });

                    return false;
                }
            }

            boardSubmit();
        });

        const boardSubmit = () => {
            let ajaxData = newFormData(boardForm);

            // 내용 사용시
            if(boardUseConfig.contents) {
                ajaxData.append('contents', tinymce.get('contents').getContent());
            }

            // 팝업 사용시
            if(boardUseConfig.popup) {
                ajaxData.append('popup_contents', tinymce.get('popup_contents').getContent());
            }

            // plupload 사용시
            if(boardUseConfig.plupload) {
                ajaxData.append('plupload_file', JSON.stringify(pluploadFile));
            }

            callMultiAjax(dataUrl, ajaxData);
        }
    </script>

    @include("popup.board.{$boardConfig['popup_style']}-popup-script")
@endsection
