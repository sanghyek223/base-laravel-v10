<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>{{ env('APP_NAME') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>
<body style="margin:0; padding:0; ">
<table style="width:100%; max-width:650px; margin:0 auto; padding:0; border:1px solid #ddd; border-collapse:collapse; border-spacing:0; box-sizing:border-box; letter-spacing:-1px; ">
    <tbody>
    <tr>
        <td style="height:60px; text-align:center; font-family:'Malgun Gothic', '맑은고딕', '돋움', 'dotum', sans-serif; font-size:20px; font-weight: bold;">
            {{ $mail->subject }}
        </td>
    </tr>

    <tr>
        <td style="padding:40px 50px 20px; font-size:26px; line-height:1.7; font-family:'Malgun Gothic', '맑은고딕', '돋움', 'dotum', sans-serif; box-sizing:border-box; ">
            {!! $mail->contents !!}
        </td>
    </tr>

    @include('admin.mail.template.common-template')
    </tbody>
</table>
</body>
</html>