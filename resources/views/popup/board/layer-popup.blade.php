@foreach($layerPopups as $board)
    @php
        /*

        팝업 세팅시 추가되는 css

        크기조절 : width:auto; min-width:팝업넓이; max-width:팝업넓이; min-height:팝업높이; max-height:팝업높이;
        위치조절 : margin:0; margin-top:팝업위치위에서; margin-left:팝업위치왼쪽에서;

        ex) 크기조절
        <div class="popup-contents" style="width:auto; min-width:600px; max-width:600px; min-height:600px; max-height:600px;">

        ex) 위치조절
        <div class="popup-contents" style="margin:0; margin-top:100px; margin-left:100px;">

        ex) 크기 + 위치조절
        <div class="popup-contents" style="width:auto; min-width:600px; max-width:600px; min-height:600px; max-height:600px; margin:0; margin-top:100px; margin-left:100px;">

        */

        $popup = $board->popups;
        $contentsStyle = "width: auto; min-width: {$popup->width}px; max-width: {$popup->width}px; min-height: {$popup->height}px; max-height: {$popup->height}px; margin-top: {$popup->position_y}px; margin-left: {$popup->position_x}px;"
    @endphp

    <div id="board-popup-{{ $board->sid ?? 0 }}" class="popup-wrap" style="display: block;">
        @switch($popup->popup_skin)
            @case('1')
                <div class="popup-contents type1" style="{{ $contentsStyle }}">
                    @include("popup.board.template1")
                </div>
                @break

            @default
                @break
        @endswitch
    </div>
@endforeach
