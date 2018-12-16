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

    <title>{{$activity->name}}</title>

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
                @foreach($index_ads as $index_ad)
                    <div class="aui-slide-node bg-dark">
                        <img src="{{$index_ad['img']}}?imageView2/1/w/600/h/350/interlace/1/q/75"/>
                    </div>
                @endforeach
            </div>
            <div class="aui-slide-page-wrap" style="z-index: 9;"><!--分页容器--></div>
        </div>
    </div>

    <!--大赛数据-->
    <div style="background: #FFF;">
        <div class="aui-row aui-text-center aui-padded-t-15 aui-padded-b-15">
            <div class="aui-col-xs-4" style="border-right: 1px solid #EEEEEE;">
                <div class="main-color aui-font-size-20">
                    {{$activity->real_join_num}}
                </div>
                <div class="aui-font-size-14 text-grey-999 aui-margin-t-5">
                    参赛选手
                </div>
            </div>
            <div class="aui-col-xs-4" style="border-right: 1px solid #EEEEEE;">
                <div class="main-color aui-font-size-20">
                    {{$activity->vote_num}}
                </div>
                <div class="aui-font-size-14 text-grey-999 aui-margin-t-5">
                    活动热度
                </div>
            </div>
            <div class="aui-col-xs-4" style="border-right: 1px solid #EEEEEE;">
                <div class="main-color aui-font-size-20">
                    {{$activity->show_num}}
                </div>
                <div class="aui-font-size-14 text-grey-999 aui-margin-t-5">
                    访问量
                </div>
            </div>
        </div>
    </div>
    <!--搜索及报名-->
    <div class="aui-margin-t-10">
        <div style="background: #FFF;">
            <div class="aui-row">
                <div class="aui-col-xs-9">
                    <input id="search_word" name="search_word" class="aui-input aui-font-size-14"
                           value="{{$con_arr['search_word_by_code_or_name']}}"
                           placeholder="搜索选手名称或编号" style="margin-left: 25px;">
                </div>
                <div class="aui-col-xs-3">
                    <img src="{{URL::asset('/img/search_btn.png')}}" style="width: 24px;height: 24px;"
                         class="aui-margin-r-15 aui-font-size-12 aui-pull-right aui-margin-t-10"
                         onclick="clickSearch({{$activity->id}});">
                </div>
            </div>
        </div>
        <div class="aui-row" style="background: #f5f5f5;">
            <div class="aui-col-xs-3" style="background: #f0f0f0;height: 90px;">
                <div class="aui-text-center aui-margin-t-15" onclick="clickIntro({{$activity->id}});">
                    <div><img src="{{URL::asset('/img/file_red.png')}}" style="width: 26px;margin: auto;"></div>
                    <div class="aui-margin-t-10 aui-font-size-12 main-color">大赛简介</div>
                </div>
            </div>
            <div class="aui-col-xs-9" style="color: #6E6E6E;height: 90px;">
                @if(($activity->apply_status=='0' || $activity->apply_status=='1') && $activity->valid_status=='0')
                    <div class="aui-text-center aui-padded-t-15">
                        <div class="aui-flex-col aui-flex-middle aui-flex-center">
                            <img src="{{asset('/img/vote_status_0_1.png')}}" style="width: 24px;height: 24px;position: relative;top: -2px;">
                            <span class="aui-margin-l-5 aui-font-size-18" style="color: #101010;">
                                报名中
                            </span>
                        </div>
                    </div>
                    <div class="aui-text-center aui-margin-t-10 aui-font-size-12">
                        选手个人页全部分享后即可正式开赛
                    </div>
                @else
                    <div class="aui-text-center aui-padded-t-15">
                        <div class="aui-flex-col aui-flex-middle aui-flex-center">
                            <img src="{{URL::asset('/img/alarm.png')}}" style="width: 16px;height: 16px;"><span
                                    class="aui-margin-l-5 aui-font-size-12">
                            {{--一旦激活，则设定投票结束时间 By TerryQi 2018-09-13--}}
                                距离投票结束还有
                        </span>
                        </div>
                    </div>
                    <div class="aui-text-center aui-margin-t-10" style="color: #101010;">
                        {{--活动已经结束--}}
                        @if($activity->vote_status==2)
                            <span class="aui-font-size-18">活动已经结束</span>
                        @else
                            <span class="aui-font-size-18" id="rest_days"></span><span class="aui-font-size-12">天</span>
                            <span class="aui-font-size-18 aui-margin-l-10" id="rest_hours"></span><span
                                    class="aui-font-size-12">时</span>
                            <span class="aui-font-size-18 aui-margin-l-10" id="rest_minutes"></span><span
                                    class="aui-font-size-12">分</span>
                            <span class="aui-font-size-18 aui-margin-l-10" id="rest_seconds"></span><span
                                    class="aui-font-size-12">秒</span>
                        @endif
                    </div>
                @endif

                {{--<div class="aui-text-center aui-padded-t-15">--}}
                    {{--<div class="aui-flex-col aui-flex-middle aui-flex-center">--}}
                        {{--<img src="{{URL::asset('/img/alarm.png')}}" style="width: 16px;height: 16px;"><span--}}
                                {{--class="aui-margin-l-5 aui-font-size-12">--}}
                            {{--一旦激活，则设定投票结束时间 By TerryQi 2018-09-13--}}
                            {{--@if(($activity->apply_status=='0' || $activity->apply_status=='1') && $activity->valid_status=='0')--}}
                                {{--距离报名结束还有--}}
                            {{--@else--}}
                                {{--距离投票结束还有--}}
                            {{--@endif--}}
                        {{--</span>--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<div class="aui-text-center aui-margin-t-10" style="color: #101010;">--}}
                    {{--活动已经结束--}}
                    {{--@if($activity->vote_status==2)--}}
                        {{--<span class="aui-font-size-18">活动已经结束</span>--}}
                    {{--@else--}}
                        {{--<span class="aui-font-size-18" id="rest_days"></span><span class="aui-font-size-12">天</span>--}}
                        {{--<span class="aui-font-size-18 aui-margin-l-10" id="rest_hours"></span><span--}}
                                {{--class="aui-font-size-12">时</span>--}}
                        {{--<span class="aui-font-size-18 aui-margin-l-10" id="rest_minutes"></span><span--}}
                                {{--class="aui-font-size-12">分</span>--}}
                        {{--<span class="aui-font-size-18 aui-margin-l-10" id="rest_seconds"></span><span--}}
                                {{--class="aui-font-size-12">秒</span>--}}
                    {{--@endif--}}
                {{--</div>--}}

            </div>
        </div>
        @if($activity->apply_status=='1')
            <div style="background: #FFF;">
                <div class="aui-text-center aui-padded-t-15 aui-padded-b-15">
                    <span class="aui-btn" style="height: 36px;line-height: 36px;width: 140px;"
                          onclick="clickApply({{$activity->id}})">立即报名</span>
                </div>
            </div>
        @endif
    </div>

    <!--排序选项-->
    <div class="" style="background: #FFFFFF;">
        <div class="aui-tab" id="tab" style="margin-left: 40px;margin-right: 40px;">
            {{--2018.11.21 阿伟提出默认按照时间倒序--}}
            <div class="aui-tab-item aui-font-size-12 {{array_key_exists('created_at',$con_arr['orderby'])?'aui-active':''}}"
                 onclick="showVoteUser('created_at',{{$activity->id}})">最新参与
            </div>
            <div class="aui-tab-item aui-font-size-12 {{array_key_exists('show_num',$con_arr['orderby'])?'aui-active':''}}"
                 onclick="showVoteUser('show_num',{{$activity->id}})">
                默认排序
            </div>
            <div class="aui-tab-item aui-font-size-12 {{array_key_exists('vote_num',$con_arr['orderby'])?'aui-active':''}}"
                 onclick="showVoteUser('vote_num',{{$activity->id}})">人气排行
            </div>
        </div>
        <!--选手列表-->
        <div class="aui-margin-t-5">
            <div class="aui-row aui-padded-5">
                @foreach($vote_users as $vote_user)
                    <div class="aui-col-xs-6 vote-user-div {{$vote_user->show_flag=='true'?'':'aui-hide'}}"
                         onclick="clickPerson('{{$vote_user->id}}');">
                        <div class="aui-margin-5">
                            <img src="{{$vote_user->img_arr[0]}}{{strpos($vote_user->img_arr[0],'?')==false?'?imageView2/1/w/270/h/360/interlace/1/q/75':'/w/270/h/360'}}"
                                 style="border-top-left-radius: 3px;border-top-right-radius: 3px;width: 100%;height: 240px;">
                            <div style="background: #F5F5F5;">
                                <div class="aui-row aui-padded-t-10">
                                    <span class="aui-margin-l-5 aui-font-size-14 text-oneline"
                                          style="height: 28px;line-height: 28px;">{{$vote_user->name}}</span>
                                </div>
                                <div class="aui-row aui-padded-t-5">
                                    <span class="aui-margin-l-5 font-size-14 text-grey-999"
                                          style="height: 24px;line-height: 24px;">
                                        编号：{{$vote_user->code}}
                                    </span>
                                    <span class="aui-margin-r-15 aui-pull-right aui-font-size-14 main-color text-oneline"
                                          style="height: 24px;line-height: 24px;">
                                        {{$vote_user->vote_num}}℃
                                    </span>
                                </div>
                                <div class="aui-padded-t-10 aui-padded-b-10 aui-text-center">
                                    <span class="aui-btn" style="height: 32px;line-height: 32px;width: 90%;">支持</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        {{--显示更多按钮--}}
        <div id="show-more-div" class="aui-text-center aui-padded-t-15 aui-padded-b-15" onclick="clickMore();">
            <div style="border: 1px solid #EA5858;width: 86px;line-height: 30px;border-radius: 15px;display: inline-block;"
                 class="aui-text-center main-color aui-font-size-12 aui-margin-r-15">显示更多
            </div>
        </div>
    </div>



    {{--机构介绍--}}
    @if($activity->jg_intro_html)
        <div class="aui-margin-t-10" style="background: #FFFFFF;">
            <div class="aui-padded-t-10 aui-text-center">
                <span class="aui-font-size-16">机构介绍</span>
            </div>
            <div class="aui-text-center">
                <div class="aui-padded-15">
                    {!! $activity->jg_intro_html !!}
                </div>
            </div>
        </div>
    @endif

    <!--活动简介-->
    <div class="aui-margin-t-10" style="background: #FFFFFF;">
        <div class="aui-padded-t-10 aui-padded-b-15 aui-text-center">
            <span class="aui-font-size-16">活动简介</span>
        </div>
        @if($activity->video)
            <div class="aui-text-center aui-padded-10">
                <div class="vid-wrap">
                    <video controls>
                        <source src="{{$activity->video}}" type="video/mp4">
                    </video>
                </div>
            </div>
        @endif
        @if($activity->rule_html)
            <div class="aui-text-center">
                <div class="aui-padded-15">
                    {!! $activity->rule_html !!}
                </div>
            </div>
        @endif

        <div style="height: 20px;"></div>
    </div>
    <div style="height: 60px;"></div>
    <!--页脚-->
    <footer class="aui-bar aui-bar-tab" id="footer" style="border-top: 1px solid #f1f1f1;">
        <div class="aui-bar-tab-item" tapmode onclick="clickIndex({{$activity->id}})">
            <img src="{{URL::asset('/img/home_r.png')}}" style="width: 18px;height: 18px;margin: auto;">

            <div class="aui-bar-tab-label main-color aui-font-size-12">首页</div>
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
        });

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


        // //入口函数
        // $(function () {
        //     countDown();
        //     //进行事件处理
        //     setInterval(function () {
        //         countDown();
        //     }, 1000)
        // });

        //结束时间
        @if(($activity->apply_status=='0' || $activity->apply_status=='1') && $activity->valid_status=='0')
            var end_time_value = '{{$activity->apply_end_time}}';
        @else
            var end_time_value = '{{$activity->vote_end_time}}';

            $(function () {
                countDown();
                //进行事件处理
                setInterval(function () {
                    countDown();
                }, 1000)
            });
        @endif

        //倒计时
        function countDown() {
            var curr_timestamp = new Date().getTime();
            // alert(curr_timestamp);
            var end_timestamp = new Date(end_time_value.replace(/-/g, '/')).getTime();
            // alert(end_timestamp);
            var duration = end_timestamp - curr_timestamp;

            console.log('duration is : ', duration)

            if (duration <= 0) {
                $("#rest_days").text('0');
                $("#rest_hours").text('0');
                $("#rest_minutes").text('0');
                $("#rest_seconds").text('0');
                return
            }

            // alert(duration);
            //计算出相差天数
            var days = Math.floor(duration / (24 * 3600 * 1000))
            //计算出小时数
            var leave1 = duration % (24 * 3600 * 1000)    //计算天数后剩余的毫秒数
            var hours = Math.floor(leave1 / (3600 * 1000))
            //计算相差分钟数
            var leave2 = leave1 % (3600 * 1000)        //计算小时数后剩余的毫秒数
            var minutes = Math.floor(leave2 / (60 * 1000))
            //计算相差秒数
            var leave3 = leave2 % (60 * 1000)      //计算分钟数后剩余的毫秒数
            var seconds = Math.round(leave3 / 1000)

            $("#rest_days").text(days);
            $("#rest_hours").text(hours);
            $("#rest_minutes").text(minutes);
            $("#rest_seconds").text(seconds);
        }


        //点击赛区
        function clickZone() {
            toast_loading("获取赛区...")
            window.location.href = "{{URL::asset('/vote/zone')}}";
        }

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

        //点击大赛说明
        function clickIntro(activity_id) {
            toast_loading("大赛说明...");
            window.location.href = "{{URL::asset('/vote/intro')}}?activity_id=" + activity_id;
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

        //点击显示更多
        function clickMore() {
            $(".vote-user-div").removeClass('aui-hide');
            $("#show-more-div").addClass('aui-hide');
        }

        //点击搜索
        function clickSearch(activity_id) {
            var search_word = $("#search_word").val();
            toast_loading("搜索中...");
            window.location.href = "{{URL::asset('/vote/index')}}?activity_id=" + activity_id + "&search_word=" + search_word;
        }

        //选手排序
        function showVoteUser(orderby, activity_id) {
            toast_loading("加载中...");
            window.location.href = "{{URL::asset('/vote/index')}}?activity_id=" + activity_id + "&vote_user_order_by=" + orderby;
        }

    </script>
@endsection