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
                            <strong>{{ $reply->subject }}</strong>
                        </h4>

                        <div class="view-info">
                            <span><strong>조회수 : </strong>{{ number_format($reply->ref) }}</span>
                            <span><strong>게시일 : </strong>{{ $reply->created_at->format('Y-m-d') }}</span>
                        </div>
                    </div>

                    @if(!empty($reply->link_url))
                        <div class="view-link text-right">
                            <a href="{{ $reply->link_url }}" target="_blank">{{ $reply->link_url }}</a>
                        </div>
                    @endif

                    <div class="view-contents editor-contents">
                        {!! $reply->contents ?? '' !!}
                    </div>

                    @if($reply->files_count > 0)
                        <div class="view-attach">
                            <div class="view-attach-con">
                                <div class="con">
                                    @foreach($reply->files as $file)
                                        <a href="{{ $file->downloadUrl() }}">
                                            {{ $file->filename }}  (다운로드 : {{ number_format($file->download) }}회)
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="btn-wrap text-right">
                        <a href="{{ route('board', ['code' => $code]) }}" class="btn btn-board btn-list">목록</a>

                        @if(!isMobile() && (isAdmin() || thisPk() == $reply->u_sid))
                            <a href="{{ route('board.reply.upsert', ['code' => $code, 'b_sid' => $b_sid, 'sid' => $reply->sid]) }}" class="btn btn-board btn-modify">수정</a>
                            <a href="javascript:void(0);" class="btn btn-board btn-delete">삭제</a>
                        @endif
                    </div>
                </div>
            </div>
            <!-- //e:board -->
        </div>
    </article>
@endsection

@section('addScript')
    @include("board.default-script")

    @if(!isMobile() && (isAdmin() || thisPk() == $reply->u_sid))
        <script>
            $(document).on('click', '.btn-delete', function() {
                if (confirm('삭제 하시겠습니까?')) {
                    callAjax(dataReplyUrl, {
                        case: 'reply-delete',
                        b_sid: {{ $b_sid }},
                        sid: {{ $reply->sid }},
                    });
                }
            });
        </script>
    @endif
@endsection
