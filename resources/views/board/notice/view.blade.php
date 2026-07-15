@extends('layouts.web-layout')

@section('addStyle')
    <link href="/html/bbs/general/assets/css/board.css" rel="stylesheet">
    <link href="/assets/css/editor.css" rel="stylesheet">
@endsection

@section('contents')
    <article class="sub-contents">
        <div class="sub-conbox inner-layer">
            <!-- s:board -->
            @include('layouts.include.sub-tit-wrap')

            <div id="board" class="board-wrap">
                <div class="board-view">
                    <div class="view-contop">
                        <h4 class="view-tit">
                            @if($boardConfig['use']['category'])
                                <span class="bbs-cate">{{ $board->categoryTxt() }}</span>
                            @endif

                            <strong>{{ $board->subject }}</strong>
                        </h4>

                        <div class="view-info">
                            <span><strong>조회수 : </strong>{{ number_format($board->ref) }}</span>
                            <span><strong>게시일 : </strong>{{ $board->created_at->format('Y-m-d') }}</span>
                        </div>
                    </div>

                    @if($boardConfig['use']['link'] && !empty($board->link_url))
                        <div class="view-link text-right">
                            <a href="{{ $board->link_url }}" target="_blank">{{ $board->link_url }}</a>
                        </div>
                    @endif

                    <div class="view-contents editor-contents">
                        {!! $board->contents ?? '' !!}
                    </div>

                    @if($boardConfig['use']['plupload'] && $board->files_count > 0)
                        <div class="view-attach">
                            <div class="view-attach-con">
                                <div class="con">
                                    @foreach($board->files as $file)
                                        <a href="{{ $file->downloadUrl() }}">
                                            {{ $file->filename }}  (다운로드 : {{ number_format($file->download) }}회)
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="btn-wrap text-center">
                        <a href="{{ route('board', ['code' => $code]) }}" class="btn btn-board btn-list full-right">목록</a>

                        @if($boardConfig['use']['reply'])
                            <a href="{{ route('board.reply.upsert', ['code' => $code, 'b_sid' => $board->sid]) }}" class="btn btn-type1 btn-reply">답글</a>
                        @endif

                        @if(!isMobile() && (isAdmin() || thisPk() == $board->u_sid))
                            <a href="{{ route('board.upsert', ['code' => $code, 'sid' => $board->sid]) }}" class="btn btn-board btn-modify">수정</a>
                            <a href="javascript:void(0);" class="btn btn-board btn-delete board-btn-delete">삭제</a>
                        @endif
                    </div>

                    @if($boardConfig['use']['comment'])
                        <!-- 댓글 -->
                        <div class="comment-wrap">
                            <div class="tit">
                                댓글 {{ number_format($board->comments_count) }}
                            </div>

                            <form id="comment-frm" method="post" data-case="comment-create">
                                <fieldset>
                                    <legned class="hide">댓글 입력</legned>
                                    <div class="comment-write-wrap mb-10">
                                        <input type="text" name="comment_writer" id="comment_writer" class="form-item" placeholder="작성자 이름 또는 닉네임 입력" value="{{ thisUser()->name_kr ?? '' }}">
                                    </div>

                                    <div class="comment-write-wrap">
                                        <textarea name="comment" id="comment" class="form-item" placeholder="댓글을 입력해주세요."></textarea>
                                        <button type="submit" class="btn btn-submit">등록</button>
                                    </div>
                                </fieldset>
                            </form>

                            @include("board.{$code}.comment.list")
                        </div>
                    @endif
                </div>
            </div>
            <!-- //e:board -->
        </div>
    </article>
@endsection

@section('addScript')
    @include("board.default-script")

    <script>
        @if(!isMobile() && (isAdmin() || thisPk() == $board->u_sid))

        $(document).on('click', '.board-btn-delete', function() {
            if (confirm('삭제 하시겠습니까?')) {
                callAjax(dataUrl, { case: 'board-delete', sid: {{ $board->sid }} });
            }
        });

        @endif

        @if($boardConfig['use']['comment'] /* 댓글 사용시 */)

        const commentForm = '#comment-frm';

        $(document).on('submit', commentForm, function () {
            const writer = $('#comment_writer');
            if (isEmpty(writer.val())) {
                alert('작성자를 입력해주세요.');
                writer.focus();
                return false;
            }

            const comment = $('#comment');
            if (isEmpty(comment.val())) {
                alert('댓글을 입력해주세요.');
                comment.focus();
                return false;
            }

            let ajaxData = formSerialize(commentForm);
            ajaxData.b_sid = {{ $board->sid }};

            callAjax(dataUrl, ajaxData);
        });

        $(document).on('click', '.comment-btn-reply', function () {
            const _this = $(this).closest('li');
            let ajaxData = _this.data();
            ajaxData.case = 'comment-postform';
            ajaxData.action = 'create';

            callbackAjax(dataUrl, ajaxData, function (data, error) {
                if (error) {
                    ajaxErrorData(error);
                    return false;
                }

                _this.after(data.upsert);
            });
        });

        $(document).on('click', '.comment-btn-update', function () {
            const _this = $(this).closest('li');
            let ajaxData = _this.data();
            ajaxData.case = 'comment-postform';
            ajaxData.action = 'update';

            callbackAjax(dataUrl, ajaxData, function (data, error) {
                if (error) {
                    ajaxErrorData(error);
                    return false;
                }

                _this.find('.comment-contents').hide();
                _this.append(data.upsert);
            });
        });

        $(document).on('click', '.comment-btn-delete', function () {
            if (confirm('댓글을 삭제 하시겠습니까?')) {
                callAjax(dataUrl, {
                    'case': 'comment-delete',
                    'b_sid': {{ $board->sid }},
                    'sid': $(this).closest('li').data('sid'),
                });
            }
        });

        $(document).on('click', '#reply_comment_submit', function () {
            const writer = $('#reply_comment_writer');
            if (writer.length > 0 && isEmpty(writer.val())) {
                alert('작성자를 입력해주세요.');
                writer.focus();
                return false;
            }

            const comment = $('#reply_comment');
            if (isEmpty(comment.val())) {
                alert('댓글을 입력해주세요.');
                comment.focus();
                return false;
            }

            let ajaxData = $(this).closest('.comment-write-wrap').data();
            ajaxData.b_sid = {{ $board->sid }};
            ajaxData.comment_writer = writer.val();
            ajaxData.comment = comment.val();

            callAjax(dataUrl, ajaxData);
        });

        $(document).on('click', '#reply_comment_cancel', function () {
            const _this = $(this);
            const action = _this.closest('.comment-write-wrap').data('case');

            if (action === 'comment-create') {
                _this.closest('li').remove();
            } else {
                _this.closest('li').find('.comment-contents').show();
                _this.closest('.comment-write-wrap').remove();
            }
        });

        @endif
    </script>
@endsection
