<div class="popup-wrap type3" style="display: block;">
    <div class="popup-rolling-wrap js-popup-rolling inner-layer">
        @foreach($rollingPopups as $board /* 게시판 팝업 */)
            @php($popup = $board->popups)

            <div id="board-popup-{{ $board->sid ?? 0 }}" class="popup-contents">
                @switch($popup->popup_skin ?? '1')
                    @case('1')
                        @include("popup.board.template1")
                        @break

                    @default
                        @break
                @endswitch
            </div>
        @endforeach
    </div>
</div>
