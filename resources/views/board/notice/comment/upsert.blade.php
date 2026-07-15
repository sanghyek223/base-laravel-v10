@if($action === 'create')
    <li class="comment-reply {{ ($comment->depth2 != 0) ? 'comment-reply2' : '' }}">
        <div class="comment-write-wrap mb-10">
            <input type="text" name="reply_comment_writer" id="reply_comment_writer" class="form-item" placeholder="작성자 이름 또는 닉네임 입력" value="{{ thisUser()->name_kr ?? '' }}">
        </div>

        @include("board.{$code}.comment.include.postform")
    </li>
@else
    @include("board.{$code}.comment.include.postform")
@endif