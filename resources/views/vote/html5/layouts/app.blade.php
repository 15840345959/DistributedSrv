<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="maximum-scale=1.0, minimum-scale=1.0, user-scalable=0, initial-scale=1.0, width=device-width"/>
    <meta name="format-detection" content="telephone=no,email=no,date=no,address=no">
    <link rel="Bookmark" href="{{ URL::asset('img/favor.ico') }}">
    <link rel="Shortcut Icon" href="{{ URL::asset('img/favor.ico') }}"/>

    <link rel="stylesheet" type="text/css" href="{{ URL::asset('/css/aui/aui.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('/css/aui/aui-flex.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('/css/aui/aui-slide.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('/css/vote/vote.css') }}"/>
    <![endif]-->
</head>
<body>


<script type="text/javascript" src="{{ URL::asset('dist/lib/jquery/1.9.1/jquery.min.js') }}"></script>
<!--aui-->
<script type="text/javascript" src="{{ URL::asset('/js/aui/aui-toast.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('/js/aui/aui-dialog.js') }}"></script>
{{--common--}}
<script type="text/javascript" src="{{ URL::asset('/js/html5/common.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('/js/isart/apiTool.js') }}"></script>
{{--微信相关--}}
<script src="https://res.wx.qq.com/open/js/jweixin-1.2.0.js" type="text/javascript" charset="utf-8"></script>
@yield('content')

</body>
</html>

@yield('script')