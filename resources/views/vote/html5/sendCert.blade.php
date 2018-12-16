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

    <title>下载电子证书</title>


    <div class="aui-text-center" style="margin-top: 40px;">
        <span class="aui-font-size-14">扫码关注进行证书下载</span>
    </div>
    <div class="aui-text-center" style="margin-top: 5px;">
        <img src="{{URL::asset('/img/isart_fwh.jpg')}}" style="width:180px;height: 180px;margin: auto;">
    </div>
    <div class="aui-text-center"
         style="margin-top: 40px;border-bottom: 1px solid #FF5959;margin-left: 70px;margin-right: 70px;">
    </div>
    <div class="aui-text-center aui-margin-t-15" style="margin-top: 5px;">
        <span class="aui-font-size-14">回复选手姓名或参赛编号</span>
    </div>
    @if($vote_user)
        <div class="aui-text-center aui-margin-t-15" style="margin-top: 5px;">
            <span class="aui-font-size-14">例如在公众号回复</span>
        </div>
        <div class="aui-text-center aui-margin-t-15" style="margin-top: 5px;">
            <span class="aui-font-size-14">{{$vote_user->code}} 或 {{$vote_user->name}}，获得参赛证书</span>
        </div>
    @endif

    <div class="aui-text-center" style="margin-top: 5px;">
        <span class="aui-font-size-14 aui-text-center main-color" onclick="clickBack();">返回上一页></span>
    </div>

    <div style="height: 60px;"></div>


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


    </script>
@endsection