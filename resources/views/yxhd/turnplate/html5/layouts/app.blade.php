<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html style="background:#ffe17a;overflow-x:hidden;" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="Bookmark" href="{{ URL::asset('img/favor.ico') }}">
    <link rel="Shortcut Icon" href="{{ URL::asset('img/favor.ico') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('/css/aui/aui.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('/css/aui/aui-flex.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('/css/yxhd/turnplate/html5/style.css') }}"/>
    {{--common--}}
    <script type="text/javascript" src="{{ URL::asset('/js/yxhd/turnplate/html5/jquery-1.10.2.js') }}"></script>
    <!--aui-->
    <script type="text/javascript" src="{{ URL::asset('/js/aui/aui-toast.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('/js/aui/aui-dialog.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('/js/html5/common.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('/js/yxhd/turnplate/html5/doT.min.js') }}"></script>

    {{--微信相关--}}
    <script src="https://res.wx.qq.com/open/js/jweixin-1.2.0.js" type="text/javascript" charset="utf-8"></script>
    <![endif]-->
</head>
<style type="text/css">
    html, body {
        line-height: 22px;
        height: 100% !important;
        background-color: #f1f1f1;
    }
</style>
<body style="background:#ffe17a;overflow-x:hidden;" class="aui-text-center aui-font-size-14">


@yield('content')

</body>
</html>

@yield('script')