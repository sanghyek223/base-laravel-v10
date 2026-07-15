<div class="comment-list-wrap">
    <ul>
        @foreach($comments as $comment)
            @include("board.{$code}.comment.include.list-li", ['row' => $comment])


            @foreach($comment->commentDepth1() as $depth1)
                @include("board.{$code}.comment.include.list-li", ['row' => $depth1])

                @foreach($depth1->commentDepth2() as $depth2)
                    @include("board.{$code}.comment.include.list-li", ['row' => $depth2])
                @endforeach
            @endforeach
        @endforeach
    </ul>
</div>

<div class="paging-wrap">
    {{ $comments->links('pagination::custom') }}
</div>