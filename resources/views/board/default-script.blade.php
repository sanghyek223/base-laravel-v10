<script>
    const dataUrl = '{{ route('board.data', ['code' => $code]) }}';
    const dataReplyUrl = '{{ route('board.reply.data', ['code' => $code]) }}'
    const boardUseConfig = @json($boardConfig['use']);
    const popupMinWidth = 500;
    const popupMinHeight = 600;
    const boardForm = '#board-frm';
    const replyForm = '#reply-frm';

    const getPK = (_this) => {
        return $(_this).closest('li').data('sid');
    }
</script>
<script src="{{ asset('plugins/tinymce/tinymce.min.js') }}"></script>
<script src="{{ asset('plugins/plupload/2.3.6/plupload.full.min.js') }}"></script>
<script src="{{ asset('plugins/plupload/2.3.6/jquery.plupload.queue/jquery.plupload.queue.min.js') }}"></script>
<script src="{{ asset('script/app/plupload-tinymce.common.js') }}?v={{ config('site.app.asset_version') }}"></script>
