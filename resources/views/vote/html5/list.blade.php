@extends('vote.html5.layouts.app')

@section('content')

    <style type="text/css">
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

    <title>排名</title>

    <!--轮播+公告-->
    <div style="position: relative;">
    @if($activity->notice_text)
        <!--公告部分-->
            <div style="width: 100%;z-index: 99;position: absolute;top: 0rem;height: 36px;"
                 class="main-bg gg-trans">
                <div class="aui-row" style="padding-top: 7px;">
                    <div class="aui-col-xs-1">
                        <img src="../img/laba.png" class="aui-margin-l-10"
                             style="width: 20px;height: 20px;">
                    </div>
                    <div class="aui-col-xs-11">
                    <span class="aui-margin-l-10 text-oneline" style="width: 90%;"
                          onclick="clickNotice('{{$activity->notice_url}}')">
                        <marquee direction="left"
                                 onmouseover="this.stop()" onmouseout="this.start()">
                                <a href="" class="aui-text-white">{{$activity->notice_text}}</a>
                        </marquee>
                </span>
                    </div>
                </div>
            </div>
    @endif

    <!--轮播图-->
        <div id="aui-slide">
            <div class="aui-slide-wrap">
                @foreach($pm_ads as $pm_ad)
                    <div class="aui-slide-node bg-dark">
                        <img src="{{$pm_ad['img']}}?imageView2/1/w/600/h/350/interlace/1/q/75"/>
                    </div>
                @endforeach
            </div>
            <div class="aui-slide-page-wrap" style="z-index: 9;"><!--分页容器--></div>
        </div>
    </div>

    {{--下载证书按钮--}}
    {{--<div class="aui-text-center aui-margin-t-10 aui-padded-b-15">--}}
    {{--<img src="{{URL::asset('/img/get_cert_btn.jpg')}}" style="width: 50%;margin: auto;border-radius: 3px;"--}}
    {{--onclick="clickSendCert('',{{$activity->id}});">--}}
    {{--</div>--}}

    <!--排名列表-->
    <div class="aui-margin-t-10" style="background: #FFF;">

        <div class="aui-border-b aui-padded-t-10 aui-padded-b-10">
            <span class="aui-margin-l-10 aui-font-size-14">评选结果</span>
            <span class="aui-margin-r-10 aui-pull-right aui-font-size-14">热度排名</span>
        </div>
        <!--排名列表-->
        @foreach($vote_users as $vote_user)
            <div class="aui-row aui-margin-l-10" style="border-bottom: 1px solid #f1f1f1;"
                 onclick="clickPerson({{$vote_user->id}})">
                <div class="aui-col-xs-10">
                    <div class="aui-flex-col aui-flex-middle aui-padded-t-10 aui-padded-b-10">
                        @if($vote_user->curr_pm == 1)
                            <img src="{{URL::asset('/img/first_prize_icon.png')}}" style="width: 16px;height: 26px;"
                                 class="aui-margin-l-10">
                        @elseif($vote_user->curr_pm == 2)
                            <img src="{{URL::asset('/img/second_prize_icon.png')}}" style="width: 16px;height: 26px;"
                                 class="aui-margin-l-10">
                        @elseif($vote_user->curr_pm == 3)
                            <img src="{{URL::asset('/img/third_prize_icon.png')}}" style="width: 16px;height: 26px;"
                                 class="aui-margin-l-10">
                        @else
                            <span style="width: 16px;height: 26px;" class="aui-margin-l-10 text-grey-CCC">
                               {{$vote_user->curr_pm}}
                           </span>
                        @endif

                        {{--@if($vote_user->user)--}}
                            {{--<img src="{{$vote_user->user->avatar}}" style="width: 54px;height: 54px;"--}}
                                 {{--class="aui-margin-l-15 aui-img-round">--}}
                        {{--@else--}}
                            {{--<img src="{{$vote_user->img_arr[0]}}{{strpos($vote_user->img_arr[0],'?')==false?'?imageView2/1/w/300/h/300/interlace/1/q/75':'/w/300/h/300'}}"--}}
                                 {{--style="width: 54px;height: 54px;"--}}
                                 {{--class="aui-margin-l-15 aui-img-round">--}}
                        {{--@endif--}}

                        <img src="{{$vote_user->img_arr[0]}}{{strpos($vote_user->img_arr[0],'?')==false?'?imageView2/1/w/300/h/300/interlace/1/q/75':'/w/300/h/300'}}"
                             style="width: 54px;height: 54px;"
                             class="aui-margin-l-15 aui-img-round">

                        <span class="aui-margin-l-10">
                            <div>
                                <div class="aui-font-size-14 text-oneline" style="max-width: 180px;">
                                    {{$vote_user->name}}
                                </div>
                                <div class="aui-flex-col aui-flex-middle aui-margin-t-5 aui-font-size-14 text-grey-999">
                                    <span class="">票数</span>
                                    <span class="aui-margin-l-10">{{$vote_user->vote_num}}℃</span>
                                    <span class="aui-margin-l-15">
                                        @if($vote_user->pm_bd > 0)
                                            <img src="{{URL::asset('/img/list_up_icon.png')}}"
                                                 style="width: 15px;height: 15px;">
                                        @endif
                                        @if($vote_user->pm_bd == 0)
                                            <img src="{{URL::asset('/img/list_ping_icon.png')}}"
                                                 style="width: 15px;height: 15px;">
                                        @endif
                                        @if($vote_user->pm_bd < 0)
                                            <img src="{{URL::asset('/img/list_down_icon.png')}}"
                                                 style="width: 15px;height: 15px;">
                                        @endif

                                    </span>
                                    <span class="aui-margin-l-5">{{abs($vote_user->pm_bd)}}</span>
                                </div>
                            </div>
                        </span>
                    </div>
                </div>
                <div class="aui-col-xs-2">
                    @if($activity->vote_status=='2')
                        <span class="main-color aui-font-size-12 aui-pull-right aui-margin-r-10"
                              style="margin-top: 26px;">{{$vote_user->pm_str?$vote_user->pm_str:''}}
                    </span>
                    @else
                        <span class="aui-pull-right aui-margin-r-5" style="margin-top: 26px;">
                        <span class="aui-iconfont aui-icon-right text-grey-999"></span>
                    </span>
                    @endif
                </div>
            </div>
        @endforeach

    </div>


    <div style="height: 60px;"></div>
    <!--页脚-->
    <footer class="aui-bar aui-bar-tab" id="footer" style="border-top: 1px solid #f1f1f1;">
        <div class="aui-bar-tab-item" tapmode onclick="clickIndex({{$activity->id}})">
            <img src="{{URL::asset('/img/home_n.png')}}" style="width: 18px;height: 18px;margin: auto;">

            <div class="aui-bar-tab-label aui-font-size-12">首页</div>
        </div>
        <div class="aui-bar-tab-item" tapmode onclick="clickPresent({{$activity->id}})">
            <img src="{{URL::asset('/img/present_n.png')}}" style="width: 18px;height: 18px;margin: auto;">

            <div class="aui-bar-tab-label aui-font-size-12">奖品</div>
        </div>
        <div class="aui-bar-tab-item" tapmode onclick="clickApply({{$activity->id}})">
            <img src="{{URL::asset('/img/apply.png')}}"
                 style="width: 38px;height: 38px;margin: auto;margin-top: -18px;">

            <div class="aui-bar-tab-label aui-font-size-12">参赛</div>
        </div>
        <div class="aui-bar-tab-item" tapmode onclick="clickList({{$activity->id}})">
            <img src="{{URL::asset('/img/list_r.png')}}" style="width: 18px;height: 18px;margin: auto;">

            <div class="aui-bar-tab-label main-color aui-font-size-12">排名</div>
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

        //自定义轮播
        var slide = new auiSlide({
            container: document.getElementById("aui-slide"), //容器
            // "width":300, //宽度
            "height": 220, //高度
            "speed": 500, //速度
            "autoPlay": 3000, //自动播放
            "loop": true,//是否循环
            "pageShow": true,//是否显示分页器
            "pageStyle": 'dot', //分页器样式，分dot,line
            'dotPosition': 'center' //当分页器样式为dot时控制分页器位置，left,center,right
        })

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

        //点击公告
        function clickNotice(notice_url) {
            if (judgeIsAnyNullStr(notice_url)) {
                return;
            }
            toast_loading("查看公告...");
            console.log("clickNotice notice_url:" + notice_url);
            window.open(notice_url);
        }

        //点击查看选手主页
        function clickPerson(vote_user_id) {
            toast_loading("选手主页...");
            window.location.href = "{{URL::asset('/vote/person')}}?vote_user_id=" + vote_user_id;
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

        //点击查看选手主页
        function clickPerson(vote_user_id) {
            toast_loading("选手主页...");
            window.location.href = "{{URL::asset('/vote/person')}}?vote_user_id=" + vote_user_id;
        }

        //点击报名
        function clickApply(activity_id) {
            toast_loading("参赛报名...");
            window.location.href = "{{URL::asset('/vote/apply')}}?activity_id=" + activity_id;
        }

        //点击发送证书
        function clickSendCert(vote_user_id, activity_id) {
            window.location.href = "{{URL::asset('/vote/sendCert')}}?vote_user_id=" + vote_user_id + "&activity_id=" + activity_id;
        }


    </script>
@endsection