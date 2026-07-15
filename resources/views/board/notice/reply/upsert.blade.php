@extends('layouts.web-layout')

@section('addStyle')
    <link href="/html/bbs/general/assets/css/board.css" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="{{ asset('plugins/plupload/2.3.6/jquery.plupload.queue/css/jquery.plupload.queue.css') }}" />
@endsection

@section('contents')
    <article class="sub-contents">
        <div class="sub-conbox inner-layer">
            <!-- //s:board -->
            @include('layouts.include.sub-tit-wrap')

            <div id="board" class="board-wrap">
                <div class="board-write">
                    <div class="write-form-wrap">
                        <form id="reply-frm" method="post" data-b_sid="{{ $b_sid }}" data-sid="{{ $reply->sid ?? 0 }}" data-case="reply-{{ empty($reply->sid) ? 'create' : 'update' }}">
                            <fieldset>
                                <legend class="hide">글쓰기</legend>

                                <div class="write-contop text-right">
                                    <div class="help-text"><strong class="required">*</strong> 표시는 필수입력 항목입니다.</div>
                                </div>

                                <ul class="write-wrap">
                                    <li>
                                        <div class="form-tit"><strong class="required">*</strong> 작성자</div>
                                        <div class="form-con">
                                            <input type="text" name="writer" id="writer" class="form-item" value="{{ $reply->subject ?? thisUser()->name_kr }}">
                                        </div>
                                    </li>

                                    <li>
                                        <div class="form-tit"><strong class="required">*</strong> 이메일</div>
                                        <div class="form-con">
                                            <input type="text" name="email" id="email" class="form-item" value="{{ $reply->email ?? thisUser()->email }}">
                                        </div>
                                    </li>

                                    <li>
                                        <div class="form-tit"><strong class="required">*</strong> 제목</div>
                                        <div class="form-con">
                                            <input type="text" name="subject" id="subject" class="form-item" value="{{ $reply->subject ?? $board->baseReplySubject() }}">
                                        </div>
                                    </li>

                                    <li>
                                        <div class="form-tit">LINK URL</div>
                                        <div class="form-con">
                                            <input type="text" name="link_url" id="link_url" class="form-item" value="{{ $reply->link_url ?? 'https://' }}" noneSpace>
                                        </div>
                                    </li>

                                    @if(($reply->files_count ?? 0) > 0)
                                        <li>
                                            <div class="form-tit">첨부파일</div>
                                            <div class="form-con">
                                                <div class="checkbox-wrap cst">
                                                    @foreach($reply->files as $key => $file)
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

                                    <li>
                                        <div class="form-con">
                                            <textarea name="contents" id="contents" class="tinymce">{{ $reply->contents ?? $board->baseReplyContents() }}</textarea>
                                        </div>
                                    </li>

                                    {{--                                    <li>--}}
                                    {{--                                        <div class="form-con">--}}
                                    {{--                                            <div id="plupload"></div>--}}
                                    {{--                                        </div>--}}
                                    {{--                                    </li>--}}
                                </ul>

                                <div class="btn-wrap text-center">
                                    <a href="javascript:void(0);" class="btn btn-board btn-cancel">취소</a>

                                    <button type="submit" class="btn btn btn-board btn-write">
                                        {{ empty($reply->sid) ? '등록' : '수정' }}
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

            const msg = ($(replyForm).data('sid') == 0) ?
                '등록을 취소하시겠습니까?' :
                '수정을 취소하시겠습니까?';

            if (confirm(msg)) {
                location.replace('{{ route('board', ['code' => $code]) }}');
            }
        });

        // 첨부파일 (plupload)
        pluploadInit({
            multipart_params: {
                directory: '{{ $boardConfig['directory'] }}/reply',
            },
            filters: {
                max_file_size: '20mb'
            },
        });

        $(document).on('submit', replyForm, function () {
            const writer = $('#writer');
            if (isEmpty(writer.val())) {
                alert(`작성자를 입력해주세요.`);
                writer.focus();
                return false;
            }

            const email = $('#email');
            if (isEmpty(email.val())) {
                alert(`이메일을 입력해주세요.`);
                email.focus();
                return false;
            }

            const subject = $('#subject');
            if (isEmpty(subject.val())) {
                alert(`제목을 입력해주세요.`);
                subject.focus();
                return false;
            }

            let tinyVal = tinymce.get('contents').getContent(); // 내용 가져오기
            // tinyVal = tinyVal.replace(/<[^>]*>?/g, ''); // html 태그 삭제
            tinyVal = tinyVal.replace(/\&nbsp;/g, ' '); // &nbsp 삭제

            if (isEmpty(tinyVal)) {
                alert('내용을 입력해주세요.');
                return false;
            }

            // const plupload_queue = $('#plupload').pluploadQueue();
            //
            // let fileCnt = plupload_queue.files.length;
            // fileCnt = (fileCnt - previousUploadedFilesCount);
            //
            // if (fileCnt > 0) {
            //     spinnerShow();
            //     plupload_queue.start();
            //     plupload_queue.bind('UploadComplete', function(up, files) {
            //         spinnerHide();
            //
            //         if (plupload_queue.total.failed !== 0) {
            //             alert('파일 업로드 실패');
            //             location.reload();
            //             return false;
            //         }
            //
            //         // 업로드된 파일 수를 저장
            //         previousUploadedFilesCount = up.files.length;
            //         boardSubmit();
            //     });
            //
            //     return false;
            // }

            boardSubmit();
        });

        const boardSubmit = () => {
            let ajaxData = newFormData(replyForm);
            ajaxData.append('b_sid', $(replyForm).data('b_sid'));

            // 내용
            ajaxData.append('contents', tinymce.get('contents').getContent());

            // plupload
            ajaxData.append('plupload_file', JSON.stringify(pluploadFile));

            callMultiAjax(dataReplyUrl, ajaxData);
        }
    </script>
@endsection
