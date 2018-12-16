@extends('vote.html5.layouts.app')

@section('content')

    <style type="text/css">

        html, body {
            background: white;
        }

        button, .aui-btn {
            color: #FFFFFF;
            background: #EA5858;
        }

        .aui-btn:active {
            color: #FFFFFF;
            background-color: #EA0000;
        }

        .aui-tab-item.aui-active {
            color: #000000;
            border-bottom: 2px solid #EA5858;
        }

        /*公告的渐变*/
        .gg-trans {
            filter: Alpha(Opacity=70); /* for IE */
            background-color: rgba(234, 88, 88, 0.7); /*for FF*/
        }

        ::-webkit-input-placeholder {
            /* WebKit browsers */
            color: #999;
        }

        :-moz-placeholder {
            /* Mozilla Firefox 4 to 18 */
            color: #999;
        }

        ::-moz-placeholder {
            /* Mozilla Firefox 19+ */
            color: #999;
        }

        :-ms-input-placeholder {
            /* Internet Explorer 10+ */
            color: #999;
        }

        /*video自适应手机*/
        .vid-wrap {
            width: 100%;
            background: #000;
            position: relative;
            padding-bottom: 56.25%; /*需要用padding来维持16:9比例,也就是9除以16*/
            height: 0;
        }

        .vid-wrap video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

    </style>

    <title>活动奖品</title>

    <!--奖品页面-->
    <div class="aui-text-center">
        <div class="aui-padded-15">
            {!! $activity->gift_html !!}
        </div>
    </div>

    <div style="height: 20px;"></div>

    <div style="height: 60px;"></div>
    <!--页脚-->
    <footer class="aui-bar aui-bar-tab" id="footer" style="border-top: 1px solid #f1f1f1;">
        <div class="aui-bar-tab-item" tapmode onclick="clickIndex({{$activity->id}})">
            <img src="{{URL::asset('/img/home_n.png')}}" style="width: 18px;height: 18px;margin: auto;">

            <div class="aui-bar-tab-label aui-font-size-12">首页</div>
        </div>
        <div class="aui-bar-tab-item" tapmode onclick="clickPresent({{$activity->id}})">
            <img src="{{URL::asset('/img/present_r.png')}}" style="width: 18px;height: 18px;margin: auto;">

            <div class="aui-bar-tab-label main-color aui-font-size-12">奖品</div>
        </div>
        <div class="aui-bar-tab-item" tapmode onclick="clickApply({{$activity->id}})">
            <img src="{{URL::asset('/img/apply.png')}}"
                 style="width: 38px;height: 38px;margin: auto;margin-top: -18px;">

            <div class="aui-bar-tab-label aui-font-size-12">参赛</div>
        </div>
        <div class="aui-bar-tab-item" tapmode onclick="clickList({{$activity->id}})">
            <img src="{{URL::asset('/img/list_n.png')}}" style="width: 18px;height: 18px;margin: auto;">

            <div class="aui-bar-tab-label aui-font-size-12">排名</div>
        </div>
        <div class="aui-bar-tab-item" tapmode onclick="clickComplain({{$activity->id}})">
            <img src="{{URL::asset('/img/suggest_n.png')}}" style="width: 18px;height: 18px;margin: auto;">

            <div class="aui-bar-tab-label aui-font-size-12">投诉</div>
        </div>
    </footer>

@endsection

@section('script')

    <script type="text/javascript" src="{{ URL::asset('/js/aui/aui-slide.js') }}"></script>

    <script>
        var _mtac = {};
        (function () {
            var mta = document.createElement("script");
            mta.src = "http://pingjs.qq.com/h5/stats.js?v2.0.2";
            mta.setAttribute("name", "MTAH5");
            mta.setAttribute("sid", "500636629");
            var s = document.getElementsByTagName("script")[0];
            s.parentNode.insertBefore(mta, s);
        })();
    </script>

    <script type="text/javascript">


        //微信相关/////////////////////////////////////////////////////
        wx.config({!! $wxConfig !!});

        //微信配置成功后
        wx.ready(function () {
            /*
             * 进行页面分享-朋友圈
             *
             * By TerryQi
             *
             */
            wx.onMenuShareTimeline({
                title: '{{$activity->share_title}}', // 分享标题
                link: window.location.href, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                imgUrl: '{{$activity->share_img}}', // 分享图标
                success: function (ret) {
                    // 用户确认分享后执行的回调函数
                    console.log("success ret:" + JSON.stringify(ret))
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });

            //app分享
            wx.onMenuShareAppMessage({
                title: '{{$activity->share_title}}', // 分享标题
                desc: '{{$activity->share_desc}}',
                link: window.location.href, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                imgUrl: '{{$activity->share_img}}', // 分享图标
                type: 'link', // 分享类型,music、video或link，不填默认为link
                dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                success: function () {
                    // 用户确认分享后执行的回调函数
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });
        });

        ///////////////////////////////////////////////////////////////

        //入口函数
        $(function () {

        });

        //点击赛区
        function clickZone() {
            toast_loading("获取赛区...")
            window.location.href = "{{URL::asset('/vote/zone')}}";
        }

        //点击首页
        function clickIndex(activity_id) {
            toast_loading("大赛主页...");
            window.location.href = "{{URL::asset('/vote/index')}}?activity_id=" + activity_id;
        }

        //点击礼物
        function clickPresent(activity_id) {
            toast_loading("奖品说明...");
            window.location.href = "{{URL::asset('/vote/present')}}?activity_id=" + activity_id;
        }

        //点击投诉
        function clickComplain(activity_id) {
            toast_loading("我要投诉...");
            window.location.href = "{{URL::asset('/vote/complain')}}?activity_id=" + activity_id;
        }

        //点击排名
        function clickList(activity_id) {
            toast_loading("查看排名...");
            window.location.href = "{{URL::asset('/vote/list')}}?activity_id=" + activity_id;
        }

        //点击报名
        function clickApply(activity_id) {
            toast_loading("参赛报名...");
            window.location.href = "{{URL::asset('/vote/apply')}}?activity_id=" + activity_id;
        }


    </script>
@endsection