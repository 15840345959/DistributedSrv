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


    </style>

    <title>投诉建议</title>

    <!--报名表单-->
    <div style="background: white;">
        <ul class="aui-list aui-form-list aui-margin-l-10 aui-list-noborder aui-font-size-14" style="border: 0px;">
            <li class="aui-list-item aui-hide" style="border-bottom: 1px solid #f1f1f1;background: white;">
                <div class="aui-list-item-inner">
                    <div class="aui-list-item-label">
                        用户id
                    </div>
                    <div class="aui-list-item-input">
                        <input id="user_id" type="text" placeholder="活动id" class="aui-input aui-font-size-14"
                               value="{{$user->id}}">
                    </div>
                </div>
            </li>
            <li class="aui-list-item aui-hide" style="border-bottom: 1px solid #f1f1f1;background: white;">
                <div class="aui-list-item-inner">
                    <div class="aui-list-item-label">
                        活动id
                    </div>
                    <div class="aui-list-item-input">
                        <input id="activity_id" type="text" placeholder="活动id" class="aui-input aui-font-size-14"
                               value="{{$con_arr['activity_id']}}">
                    </div>
                </div>
            </li>
            <li class="aui-list-item aui-hide" style="border-bottom: 1px solid #f1f1f1;background: white;">
                <div class="aui-list-item-inner">
                    <div class="aui-list-item-label">
                        关联选手id
                    </div>
                    <div class="aui-list-item-input">
                        <input id="vote_user_id" type="text" placeholder="关联选手id" class="aui-input aui-font-size-14"
                               value="{{$con_arr['vote_user_id']}}">
                    </div>
                </div>
            </li>
            <li class="aui-list-item" style="border-bottom: 1px solid #f1f1f1;background: white;">
                <div class="aui-list-item-inner">
                    <div class="aui-list-item-label">
                        您的称呼
                    </div>
                    <div class="aui-list-item-input">
                        <input id="name" type="text" value="{{$user->nick_name}}" placeholder="请输入姓名"
                               class="aui-input aui-font-size-14">
                    </div>
                </div>
            </li>
            <li class="aui-list-item" style="border-bottom: 1px solid #f1f1f1;background: white;">
                <div class="aui-list-item-inner">
                    <div class="aui-list-item-label">
                        联系电话
                    </div>
                    <div class="aui-list-item-input">
                        <input id="phonenum" type="text" value="{{$user->phonenum}}" placeholder="请输入手机号"
                               class="aui-input aui-font-size-14">
                    </div>
                </div>
            </li>
            <li class="aui-list-item" style="border-bottom: 1px solid #f1f1f1;background: white;">
                <div class="aui-margin-t-15" style="width: 100%;">
                    <div>
                        具体内容
                    </div>
                    <div>
                <textarea id="content" class="aui-font-size-14" style="width: 100%;height:auto;border: 0px;
                          line-height: 22px;color:#666666;outline: none; overflow-y:visible"
                          rows="4"
                          placeholder="请输入您的建议或意见..."></textarea>
                    </div>
                </div>
            </li>
        </ul>

        <div class="aui-text-center aui-margin-t-15">
            <span class="aui-font-size-12 text-grey-999">大赛组委会将在24小时内解答您的问题，请准确输入联系方式</span>
        </div>
        <div class="aui-text-center aui-padded-t-15 aui-padded-b-15" onclick="clickSave();">
            <span class="aui-btn" style="height: 36px;line-height: 36px;width: 140px;">提交建议</span>
        </div>
    </div>


    <div style="padding-top: 40px;">
        <img src="{{URL::asset('/img/isart_fwh.jpg')}}" style="width: 40%;margin: auto;">
    </div>

    <div class="aui-text-center aui-padded-t-15 aui-padded-l-15 aui-padded-r-15">
        <span class="text-grey-999 aui-font-size-14" style="line-height: 22px;">扫码联系组委会</span>
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
            <img src="{{URL::asset('/img/list_n.png')}}" style="width: 18px;height: 18px;margin: auto;">

            <div class="aui-bar-tab-label aui-font-size-12">排名</div>
        </div>
        <div class="aui-bar-tab-item" tapmode onclick="clickComplain({{$activity->id}})">
            <img src="{{URL::asset('/img/suggest_r.png')}}" style="width: 18px;height: 18px;margin: auto;">

            <div class="aui-bar-tab-label main-color aui-font-size-12">投诉</div>
        </div>
    </footer>


@endsection

@section('script')

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

        //点击提交
        function clickSave() {
            //合规校验
            var name = $("#name").val();
            if (judgeIsAnyNullStr(name)) {
                dialog_show({
                    msg: "请输入姓名或昵称",
                    buttons: ['确定'],
                }, null);
                return;
            }
            var phonenum = $("#phonenum").val();
            if (judgeIsAnyNullStr(phonenum) || !isPoneAvailable(phonenum)) {
                dialog_show({
                    msg: "手机号码不正确",
                    buttons: ['确定'],
                }, null);

                return;
            }
            var content = $("#content").val();
            if (judgeIsAnyNullStr(content)) {
                dialog_show({
                    msg: "请输入具体内容",
                    buttons: ['确定'],
                }, null);
                return;
            }
            var param = {
                activity_id: $("#activity_id").val(),
                vote_user_id: $("#vote_user_id").val(),
                user_id: $("#user_id").val(),
                name: name,
                phonenum: phonenum,
                content: content,
                _token: '{{ csrf_token() }}'
            }
            toast_loading("提交中...");
            v1_complain("{{URL::asset('')}}", param, function (ret) {
                console.log("ret:" + JSON.stringify(ret));
                //提交成功
                if (ret.result == true) {
                    dialog_show({msg: "感谢参与活动，我们将在24小时内回复您，请您耐心等待", buttons: ['回到大赛']}, function (ret) {
                        toast_loading("进入大赛...");
                        window.location.href = "{{URL::asset('/vote/index')}}?activity_id={{$con_arr['activity_id']}}";
                    });
                } else {
                    dialog_show({msg: ret.message, buttons: ['确定']}, null);
                }
            })
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