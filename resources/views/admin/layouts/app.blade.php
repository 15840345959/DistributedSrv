<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta name="renderer" content="ie-comp">
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport"
          content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
    <meta http-equiv="Cache-Control" content="no-siteapp"/>
    <link rel="Bookmark" href="{{ URL::asset('img/favor.ico') }}">
    <link rel="Shortcut Icon" href="{{ URL::asset('img/favor.ico') }}"/>
    <!--[if lt IE 9]>
    <script type="text/javascript" src="{{ URL::asset('dist/lib/html5shiv.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('dist/lib/respond.min.js') }}"></script>
    <![endif]-->

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ URL::asset('css/iconfont/iconfont.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ URL::asset('dist/static/h-ui/css/H-ui.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('dist/static/h-ui.admin/css/H-ui.admin.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('dist/lib/Hui-iconfont/1.0.8/iconfont.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('dist/static/h-ui.admin/skin/default/skin.css') }}"
          id="skin"/>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('/css/common.css') }}"/>
    {{--<link rel="stylesheet" type="text/css" href="{{ URL::asset('/css/bootstrap.min.css') }}">--}}
    <!--[if IE 6]>
    <script type="text/javascript" src="{{ URL::asset('dist/lib/DD_belatedPNG_0.0.8a-min.js') }}"></script>
    <script>DD_belatedPNG.fix('*');</script>
    <![endif]-->
    <title>ISART | 管理后台</title>
</head>
<body>

<!--_footer 作为公共模版分离出去-->
<script type="text/javascript" src="{{ URL::asset('dist/lib/jquery/1.9.1/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('dist/lib/layer/2.4/layer.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('dist/static/h-ui/js/H-ui.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('dist/static/h-ui.admin/js/H-ui.admin.js') }}"></script>
<!--/_footer 作为公共模版分离出去-->

<!--请在下方写此页面业务相关的脚本-->
<script type="text/javascript" src="{{ URL::asset('dist/lib/jquery.contextmenu/jquery.contextmenu.r2.js') }}"></script>
<!--请在下方写此页面业务相关的脚本-->
<script type="text/javascript" src="{{ URL::asset('dist/lib/My97DatePicker/4.8/WdatePicker.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('dist/lib/datatables/1.10.0/jquery.dataTables.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('dist/lib/laypage/1.2/laypage.js') }}"></script>
{{--doT、md5、七牛等相关--}}
<script type="text/javascript" src="{{ URL::asset('/js/doT.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('/js/md5.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('/js/qiniu.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('/js/plupload/plupload.full.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('/js/plupload/moxie.js') }}"></script>
<!--请在下方写此页面业务相关的脚本-->
<script type="text/javascript" src="{{ URL::asset('dist/lib/jquery.validation/1.14.0/jquery.validate.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('dist/lib/jquery.validation/1.14.0/validate-methods.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('dist/lib/jquery.validation/1.14.0/messages_zh.js') }}"></script>

{{--common.js--}}
<script type="text/javascript" src="{{ URL::asset('/js/common.js') }}"></script>

@yield('content')

</body>
</html>

@yield('script')