<div class="comment-write-wrap {{ $action === 'update' ? 'mt-10' : '' }}"
    data-sid="{{ $comment->sid ?? 0 }}" data-depth1="{{ $comment->depth1 ?? '' }}" data-depth2="{{ $comment->depth2 ?? '' }}" data-case="comment-{{ $action }}">

    <textarea name="reply_comment" id="reply_comment" class="form-item" placeholder="댓글을 입력해주세요.">{!! $comment->comment ?? '' !!}</textarea>
    <button type="button" class="btn btn-submit" id="reply_comment_submit">등록</button>
    <button type="button" class="btn btn-cancel" id="reply_comment_cancel">취소</button>
</div>