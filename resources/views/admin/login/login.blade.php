<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>ISART | 管理后台</title>
    <link href="{{ URL::asset('img/favor.ico') }}" rel="shortcut icon" type="image/x-icon"/>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- common -->
    <link rel="stylesheet" href="{{ URL::asset('css/common.css') }}">
    <!--login-->
    <link rel="stylesheet" href="{{ URL::asset('css/login.css') }}">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
        body {
            background: url("{{URL::asset('/img/web_login_bg.jpg')}}") no-repeat center;
            background-size: cover;
        }

        #darkbannerwrap {
            background: url("{{URL::asset('/img/aiwrap.png')}}");
            width: 18px;
            height: 10px;
            margin: 0 0 20px -58px;
            position: relative;
        }

    </style>
</head>
<body>

<div class="login">
    <div class="message font-size-22 text-white">ISART-后台登录</div>
    <div id="darkbannerwrap"></div>

    <form action="" method="post" onsubmit="return checkValid()">
        {{csrf_field()}}
        <input id="phonenum" name="phonenum" placeholder="手机号" required="" type="text">
        <hr class="hr15">
        <input id="password" name="password" placeholder="密码" required="" type="password">
        <hr class="hr15">
        <input value="登录" style="width:100%;" type="submit">
        <hr class="hr20">
    </form>

    @if($msg)
        <div id="error_msg" class="text-danger">
            *{{$msg}}
        </div>
    @endif
</div>

<div class="copyright">© 2017-2018 by
    <a href="#" target="_blank" style=" color: rgba(255, 255, 255, 0.85);">
        ISART</a>
</div>

</body>
</html>

<script type="text/javascript">
    if (window != top) top.location.href = location.href;
</script>
<script type="text/javascript" src="{{ URL::asset('dist/lib/jquery/1.9.1/jquery.min.js') }}"></script>
<!-- common -->
<script src="{{ URL::asset('js/common.js') }}"></script>
<!-- md5 -->
<script src="{{ URL::asset('js/md5.js') }}"></script>
<script>

    //进行表单校验
    function checkValid() {
        //手机号合规校验
        var phonenum = $("#phonenum").val();
        if (judgeIsNullStr(phonenum) || !isPoneAvailable(phonenum)) {
            $("#phonenum").focus();
            return false;
        }
        var password = $("#password").val();
        if (judgeIsNullStr(password)) {
            $("#password").focus();
            return false;
        }
        //对密码进行md5加密
        $("#password").val(hex_md5(password));
        return true;
    }

</script>