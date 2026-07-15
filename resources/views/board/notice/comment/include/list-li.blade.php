<li @if($row->thread > 0) class="comment-reply {{ $row->thread > 1 ? "comment-reply2" : '' }}" @endif
    data-sid="{{ $row->sid }}" data-b_sid="{{ $row->b_sid }}" data-depth1="{{ $row->depth1 ?? '' }}" data-depth2="{{ $row->depth2 ?? '' }}">

    <div class="comment-contop">
        <span class="comment-name">{{ empty($row->deleted_at) ? $row->writer : '작성자' }}</span>
        <span class="comment-date">{{ $row->created_at->format('Y-m-d H:i') }}</span>
    </div>

    <div class="comment-contents">
        <div>{!! empty($row->deleted_at) ? $row->comment : '삭제된 댓글입니다.' !!}</div>

        @empty($row->deleted_at)
            <div class="btn-wrap text-right">
                @if($row->depth2 ===  0)
                    <a href="javascript:void(0);" class="btn btn-comment comment-btn-reply">답글</a>
                @endif

                @if(!isMobile() && (isAdmin() || thisPk() == $board->u_sid))
                    <a href="javascript:void(0);" class="btn btn-comment btn-modify comment-btn-update">수정</a>
                    <a href="javascript:void(0);" class="btn btn-comment btn-delete comment-btn-delete">삭제</a>
                @endif
            </div>
        @endempty
    </div>

</li>