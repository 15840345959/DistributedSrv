<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="bookmark" href="{{ URL::asset('/shop/static/img/favor.ico') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ URL::asset('/shop/static/img/favor.ico') }}">
    <title>小艺商城</title>
    <link href="{{ URL::asset('/shop/static/css/app.e6a8196e075c147249ecbfd3c1beaf6c.css') }}" rel="stylesheet">
</head>
<body>
<div id="app"></div>
{{--优先执行--}}
<script>
    // 设置localstorage
    window.localStorage.setItem("user_id", "{{$user->id}}");
    window.localStorage.setItem("token", "{{$user->token}}");
    window.localStorage.setItem("api_debug", "{{$debug}}"); //设置标识

</script>
<script type="text/javascript" src="{{ URL::asset('/shop/static/js/manifest.e168e3369bbf8cb65b76.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('/shop/static/js/vendor.79795110aa265a4ff15b.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('/shop/static/js/app.1d3056eb13b294214cce.js') }}"></script>
</body>
</html>