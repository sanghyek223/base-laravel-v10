@extends('layouts.web-layout')

@section('addStyle')
@endsection

@section('contents')
    <article class="sub-contents">
        <div class="sub-conbox inner-layer">
            <!-- s:login -->
            @include('layouts.include.sub-tit-wrap')

            <div class="login-wrap type5">
                <div class="login-form">
                    <form id="login-frm" method="post">
                        <fieldset>
                            <legend class="hide">로그인</legend>
                            <div class="login-tit-wrap">
                                <h3 class="login-tit">
                                    LOGIN
                                </h3>

                                <p>
                                    신경약물임상시험학회에 오신 것을 환영합니다. <br>
                                    로그인 후 신경약물임상시험학회의 다양한 정보를 확인하세요!
                                </p>
                            </div>

                            <div class="input-box">
                                <div class="form-group">
                                    <input type="text" name="uid" id="uid" class="form-item" placeholder="아이디를 입력하세요." value="{{ Cookie::has('remember_uid') ? deCryptString(Cookie::get('remember_uid')) : '' }}" noneSpace>
                                    <input type="password" name="password" id="password" class="form-item" placeholder="비밀번호를 입력하세요." noneSpace>
                                </div>

                                <button type="submit" class="btn btn-login">로그인</button>

                                <div class="checkbox-wrap cst">
                                    <label class="checkbox-group"><input type="checkbox" name="remember_me" id="remember_me" {{ Cookie::has('remember_uid') ? 'checked' : '' }}> 아이디 저장</label>
                                </div>
                            </div>

                            <div class="btn-wrap">
                                <a href="{{ route('auth.signup') }}" class="btn btn-signup">회원계정이 없으신가요? &nbsp;회원가입하기</a>
                                <a href="javascript:void(0);" class="btn btn-find">아이디/비밀번호 찾기</a>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
            <!-- //e:login-->
        </div>
    </article>
@endsection

@section('addScript')
    <script>
        const form = '#login-frm';
        const dataUrl = '{{ route('login') }}';

        $(document).on('submit', form, function () {

            const uid = $('#uid');
            if (isEmpty(uid.val())) {
                alert('아이디를 입력 해주세요.');
                uid.focus();
                return false;
            }

            const password = $('#password');
            if (isEmpty(password.val())) {
                alert('비밀번호를 입력 해주세요.');
                password.focus();
                return false;
            }

            callAjax(dataUrl, formSerialize(form));
        });
    </script>
@endsection
