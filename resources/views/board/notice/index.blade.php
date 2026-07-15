@extends('layouts.web-layout')

@section('addStyle')
    <link href="/html/bbs/general/assets/css/board.css" rel="stylesheet">
@endsection

@section('contents')
    <article class="sub-contents">
        <div class="sub-conbox inner-layer">
            <!-- s:board -->
            @include('layouts.include.sub-tit-wrap')

            <div class="sch-wrap type1">
                <form action="{{ route('board', ['code' => $code]) }}" method="get">
                    <feildset>
                        <legend class="hide">검색</legend>
                        <div class="form-group">
                            @if($boardConfig['use']['category'])
                                <select name="category" id="category" class="form-item sch-cate">
                                    @foreach($boardConfig['category']['item'] as $key => $val)
                                        <option value="{{ $key }}" {{ request()->input('category', '') == $key ? 'selected' : '' }}>{{ $val }}</option>
                                    @endforeach
                                </select>
                            @endif

                            <select name="search" id="search" class="form-item sch-cate">
                                @foreach($boardConfig['search'] as $key => $val)
                                    <option value="{{ $key }}" {{ request()->input('search', '') == $key ? 'selected' : '' }}>{{ $val }}</option>
                                @endforeach
                            </select>

                            <input type="text" name="keyword" id="keyword" value="{{ request()->input('keyword', '') }}" class="form-item sch-key" placeholder="검색어를 입력하세요.">
                            <button type="submit" class="btn btn-sch">검색</button>
                        </div>
                    </feildset>
                </form>
            </div>

            <div id="board" class="board-wrap">
                <ul class="board-list">
                    @foreach($notice ?? [] as $row)
                        <!-- 공지 -->
                        <li class="active" data-sid="{{ $row->sid }}">
                            <div class="list-con">
                                <a href="{{ route('board.view', ['code' => $code, 'sid' => $row->sid]) }}" class="ellipsis2">
                                    <div class="bbs-tit">
                                        @if($boardConfig['use']['category'])
                                            <div class="bbs-cate">{{ $row->categoryTxt() }}</div> <br>
                                        @endif

                                        <p class="ellipsis2">
                                            {{ $row->subject }}
                                        </p>

                                        {!! $row->isNew() !!}

                                        @if($boardConfig['use']['comment'])
                                            <span class="ic-cnt">{{ number_format($row->comments_count) }}</span>
                                        @endif
                                    </div>

                                    <span class="bbs-name">{{ $row->writer }}</span>
                                    <span class="bbs-date">{{ $row->created_at->format('Y-m-d') }}</span>
                                    <span class="bbs-hit">{{ number_format($row->ref) }}</span>
                                </a>
                            </div>

                            @if(!isMobile() && isAdmin())
                                <div>
                                    <div class="bbs-admin">
                                        <select class="form-item hide-select">
                                            @foreach($boardConfig['options']['hide'] as $key => $val)
                                                <option value="{{ $key }}" {{ $key == $row->hide ? 'selected' : '' }}>{{ $val }}</option>
                                            @endforeach
                                        </select>

                                        <a href="{{ route('board.upsert', ['code' => $code, 'sid' => $row->sid]) }}" class="btn btn-modify"><span class="hide">수정</span></a>
                                        <a href="javascript:void(0);" class="btn btn-delete board-delete"><span class="hide">삭제</span></a>
                                    </div>
                                </div>
                            @endif
                        </li>
                    @endforeach

                    @forelse($list ?? [] as $row)
                        <!-- 게시글 -->
                        <li class="ef01" data-sid="{{ $row->sid }}">
                            <div class="list-con">
                                <a href="{{ route('board.view', ['code' => $code, 'sid' => $row->sid]) }}" class="ellipsis2">
                                    <div class="bbs-tit">
                                        @if($boardConfig['use']['category'])
                                            <div class="bbs-cate">{{ $row->categoryTxt() }}</div> <br>
                                        @endif

                                        <p class="ellipsis2">
                                            {{ $row->subject }}
                                        </p>

                                        {!! $row->isNew() !!}

                                        @if($boardConfig['use']['comment'])
                                            <span class="ic-cnt">{{ number_format($row->comments_count) }}</span>
                                        @endif
                                    </div>

                                    <span class="bbs-name">{{ $row->writer }}</span>
                                    <span class="bbs-date">{{ $row->created_at->format('Y-m-d') }}</span>
                                    <span class="bbs-hit">{{ number_format($row->ref) }}</span>
                                </a>
                            </div>

                            @if(!isMobile() && isAdmin())
                                <div>
                                    <div class="bbs-admin">
                                        <select class="form-item hide-select">
                                            @foreach($boardConfig['options']['hide'] as $key => $val)
                                                <option value="{{ $key }}" {{ $key == $row->hide ? 'selected' : '' }}>{{ $val }}</option>
                                            @endforeach
                                        </select>

                                        <a href="{{ route('board.upsert', ['code' => $code, 'sid' => $row->sid]) }}" class="btn btn-modify"><span class="hide">수정</span></a>
                                        <a href="javascript:void(0);" class="btn btn-delete board-delete"><span class="hide">삭제</span></a>
                                    </div>
                                </div>
                            @endif
                        </li>

                        @if($boardConfig['use']['reply'])
                            @foreach($row->replies as $reply)
                                <!-- 답글 -->
                                <li class="reply" data-b_sid="{{ $row->sid }}" data-sid="{{ $reply->sid }}">
                                    <div class="list-con">
                                        <a href="{{ route('board.reply.view', ['code' => $code, 'b_sid' => $row->sid, 'sid' => $reply->sid]) }}" class="ellipsis2">
                                            <div class="bbs-tit">
                                                <p class="ellipsis2">
                                                    {{ $reply->subject ?? '' }}
                                                </p>

                                                {!! $reply->isNew() !!}
                                            </div>

                                            <span class="bbs-name">{{ $reply->writer }}</span>
                                            <span class="bbs-date">{{ $reply->created_at->format('Y-m-d') }}</span>
                                            <span class="bbs-hit">{{ number_format($reply->ref) }}</span>
                                        </a>
                                    </div>

                                    @if(!isMobile() && isAdmin())
                                        <div>
                                            <div class="bbs-admin">
                                                <select class="form-item reply-hide-select">
                                                    @foreach($boardConfig['options']['hide'] as $key => $val)
                                                        <option value="{{ $key }}" {{ $key == $reply->hide ? 'selected' : '' }}>{{ $val }}</option>
                                                    @endforeach
                                                </select>

                                                <a href="{{ route('board.reply.upsert', ['code' => $code, 'b_sid' => $row->sid, 'sid' => $reply->sid]) }}" class="btn btn-modify"><span class="hide">수정</span></a>
                                                <a href="javascript:void(0);" class="btn btn-delete reply-delete"><span class="hide">삭제</span></a>
                                            </div>
                                        </div>
                                    @endif
                                </li>
                            @endforeach
                        @endif
                    @empty
                        <!-- no data -->
                        <li class="no-data text-center">
                            <img src="/html/bbs/general/assets/image/ic_nodata.png" alt=""> <br>
                            등록된 게시글이 없습니다.
                        </li>
                    @endforelse
                </ul>

                @if(!isMobile())
                    @if(isAdmin() || empty($boardConfig['permission']['write']) || (!empty($boardConfig['permission']['write']) && in_array(thisLevel(), $boardConfig['permission']['write'])))
                        <div class="btn-wrap text-right">
                            <a href="{{ route('board.upsert', ['code' => $code]) }}" class="btn btn-type1 btn-write">등록</a>
                        </div>
                    @endif
                @endif

                <div class="paging-wrap">
                    {{ $list->links('pagination::custom') }}
                </div>
            </div>
            <!-- //e:board -->
        </div>
    </article>
@endsection

@section('addScript')
    @include("board.default-script")

    @if(!isMobile() && isAdmin())
        <script>
            $(document).on('change', '.hide-select', function() {
                const ajaxData = {
                    case: 'db-change',
                    sid: getPK(this),
                    column: 'hide',
                    value: $(this).val(),
                }

                callAjax(dataUrl, ajaxData);
            });

            $(document).on('click', '.board-delete', function() {
                const ajaxData = {
                    case: 'board-delete',
                    sid: getPK(this),
                }

                if (confirm('삭제 하시겠습니까?')) {
                    callAjax(dataUrl, ajaxData);
                }
            });

            $(document).on('change', '.reply-hide-select', function() {
                const ajaxData = {
                    case: 'db-change',
                    sid: getPK(this),
                    b_sid: $(this).closest('li').data('b_sid'),
                    column: 'hide',
                    value: $(this).val(),
                }

                callAjax(dataReplyUrl, ajaxData);
            });

            $(document).on('click', '.reply-delete', function() {
                const ajaxData = {
                    case: 'reply-delete',
                    b_sid: $(this).closest('li').data('b_sid'),
                    sid: getPK(this),
                }

                if (confirm('삭제 하시겠습니까?')) {
                    callAjax(dataReplyUrl, ajaxData);
                }
            });
        </script>
    @endif
@endsection
