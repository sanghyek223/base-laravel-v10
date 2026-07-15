<div class="popup-tit-wrap">
    <img src="/assets/image/popup/popup_logo.png" alt="">
</div>

<div class="scroll-y">
    <div class="popup-conbox">
        <div class="popup-contit-wrap">
            <h2 class="popup-contit">{{ $board->subject ?? '' }}</h2>
        </div>

        <div class="popup-con">
            {!! $popup->popup_contents ?? '' !!}
        </div>

        @if(($board->files_count ?? 0) > 0)
            <div class="popup-attach-con">
                @foreach($board->files as $key => $file)
                    <a href="{{ empty($preview) ? $file->downloadUrl() : "javascript:void(0);" }}" target="_blank">
                        {{ $file->filename }} (다운로드 : {{ number_format($file->download) }}회)
                    </a>
                @endforeach
            </div>
        @endif

        <div class="btn-wrap text-center">
            @if(!empty($popup->popup_link))
                <a href="{{ $popup->popup_link }}" class="btn btn-pop-more">자세히보기</a>
            @endif

            @if(!empty($board->link_url))
                <a href="{{ $board->link_url }}" class="btn btn-pop-link">바로가기</a>
            @endif
        </div>
    </div>
</div>

<div class="popup-footer">
    <span class="{{ empty($board->preview) ? 'btn-pop-today-close' : 'js-main-pop-close' }}" style="cursor: pointer;">오늘하루 그만보기</span>
    <a href="javascript:void(0);" class="btn full-right js-main-pop-close">닫기</a>
</div>

<button type="button" class="btn btn-pop-close js-pop-close js-main-pop-close">
    <span class="hide">팝업 닫기</span>
</button>
