@if(!empty($mail->files[0]))
    <tr>
        <td style="padding:20px 40px; font-size:15px; line-height:1.5; font-family:'Malgun Gothic', '맑은고딕', '돋움', 'dotum', sans-serif; text-align:left;">
            <div style="padding:15px 20px; background-color: #f8f8f8; border: 1px solid #e2e2e2; border-radius: 5px;">
                @foreach($mail->files as $file)
                    <div style="margin:5px 0">
                        <a href="{{ empty($preview) ? $file->downloadUrl() : "javascript:alert('미리보기 중입니다.');" }}" style="color: #191919; text-decoration: none;">
                            <img src="{{ asset('assets/image/mail/mail_file.png') }}" alt="" style="margin-right: 5px;">
                            {{ $file->filename }}
                        </a>
                    </div>
                @endforeach
            </div>
        </td>
    </tr>
@endif


@if($mail->use_btn != '9' /* 버튼 사용시 */)
    <tr>
        <td style="padding:40px 0; font-size:26px; line-height:1.7; font-family:'Malgun Gothic', '맑은고딕', '돋움', 'dotum', sans-serif; text-align:center; box-sizing:border-box; ">
            <a href="{{ $mail->link_url }}" target="_blank">
                @switch($mail->use_btn)
                    @case('1' /* 자세히보기 */)
                        <img src="{{ asset('assets/image/mail/btn_mail_datail.png') }}" alt="">
                        @break

                    @case('2' /* 바로가기 */)
                        <img src="{{ asset('assets/image/mail/btn_mail.png') }}" alt="">
                        @break
                @endswitch
            </a>
        </td>
    </tr>
@endif