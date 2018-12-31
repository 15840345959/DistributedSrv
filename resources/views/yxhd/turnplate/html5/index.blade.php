@extends('yxhd.turnplate.html5.layouts.app')

@section('content')

    <style type="text/css">
        /*提示蒙版*/
        .tip_div {
            width: 100%;
            height: 100%;
            position: fixed;
            left: 0px;
            top: 0px;
        }

        .mask_div {
            width: 100%;
            height: 100%;
            background-color: #000;
            filter: alpha(opacity=65);
            -moz-opacity: 0.65;
            opacity: 0.65;
            /*position: fixed;*/
            /*left: 0px;*/
            /*top: 0px;*/
        }
    </style>

    <title>{{$activity->name}}</title>

    <div class="aui-row aui-text-center" style="min-width: 366px;max-width: 760px;position: relative;">
        <!--大转盘部分-->
        <div style="background-image: url('{{URL::asset('/img/yxhd/turnplate/html5/bg.png')}}');background-size: 100% 100%;height:650px;min-width: 366px;max-width: 760px;">
            <div style="height: 10px" id="2F"></div>
            <!--左侧剩余次数-->
            <div style="position: absolute;top: 18px;left: 10px;width: 130px;height: 46px">
                <div style="display: inline-block;width: 110px;height: 34px;line-height: 34px;background: white;
                border-radius: 17px;color: #FF402C;box-shadow: 1px 1px 1px #A6A6A6;"
                     class="aui-font-size-12">剩余 {{$left_turnplate_num}} 次
                </div>
            </div>
            <!--剩余积分-->
            <div style="position: absolute;top: 18px;right: 10px;">
                <div style="display: inline-block;width: 110px;height: 34px;line-height: 34px;background: #FF402C;
                border-radius: 17px;color: #FFFFFF;box-shadow: 1px 1px 1px #A6A6A6;"
                     class="aui-font-size-12">共有 {{$user->score}} 积分
                </div>
            </div>
            <!--奖品图标样式-->
            <div class="banner" style="width: 100%;margin-top: 60px;">
                <!--<div class="turnplate" style="background-image:url(images/light.gif);background-size:100% 100%;height:618px">-->
                <div style="width: 366px;height:366px;margin: auto;position: relative">
                    <img src="{{URL::asset('/img/yxhd/turnplate/html5/light.gif')}}" width="385" height="667"
                         style="left: 0px;right: 0px;margin: auto;position: absolute;top: -112px">
                    <canvas class="item" id="wheelcanvas" width="380px" height="380px" style="z-index: 100"></canvas>
                    @if($left_turnplate_num==0)
                        <img class="pointer"
                             src="{{URL::asset('/img/yxhd/turnplate/html5/turnplate_pointer_grey.png')}}">
                    @else
                        <img class="pointer"
                             src="{{URL::asset('/img/yxhd/turnplate/html5/turnplate_pointer.png')}}">
                    @endif
                </div>
                <!--</div>-->
            </div>
            <!--查看奖品与按钮相关-->
            <div class="aui-row " style="padding-top:4rem">
                <div class="aui-col-xs-6">
                    <a href="#1F" name="1F">
                        <div style="display: inline-block;background: #FF402C;padding-top: 10px;padding-bottom:10px;border-radius: 5px;width: 140px;"
                             class="aui-font-size-12 aui-text-white">
                            查看奖品
                        </div>
                    </a>
                </div>
                <div class="aui-col-xs-6">
                    <div style="display: inline-block;background: #FF402C;padding-top: 10px;padding-bottom:10px;border-radius: 5px;width: 140px;"
                         class="aui-font-size-12 aui-text-white" onclick="clickShare()">
                        分享得积分
                    </div>
                </div>
            </div>
        </div>
        <!--分隔符-->
        <div>
            <img src="{{URL::asset('/img/yxhd/turnplate/html5/row.png')}}" style="width: 100%;">
        </div>

        <!--中奖记录-->
        <div class="aui-margin-t-15">
            <div class="aui-row aui-text-center" style="width: 96%;margin: auto;">
                <!--我的抽奖记录-->
                <div class="" style="background: #FF715A;">
                    <div class="aui-padded-t-10">
                        <div style="display: inline-block;background: white;width: 80px;height: 26px;line-height: 26px;color: #FF402C;border-radius: 13px;"
                             class="aui-font-size-12">中奖记录
                        </div>
                    @if($yxhdOrders->count()==0)
                        <!--没有抽奖记录-->
                            <div class="aui-padded-t-15">
                                <span class="aui-text-white aui-font-size-14">您还没有抽奖记录哦</span>
                            </div>
                            <div class="aui-padded-t-10">
                                <a href="#2F" name="2F">
                                    <span class="aui-font-size-14" style="color: #ffe17a;">去抽奖>></span>
                                </a>
                            </div>
                    @else
                        <!--抽奖记录-->
                            <div style="height: 15px;"></div>
                            <div class="aui-padded-10 aui-font-size-14 aui-text-white">
                                <div class="aui-row">
                                    <div class="aui-col-xs-4">
                                        奖项
                                    </div>
                                    <div class="aui-col-xs-4">
                                        中奖码
                                    </div>
                                    <div class="aui-col-xs-4">
                                        奖品兑换
                                    </div>
                                </div>
                                @foreach($yxhdOrders as $yxhdOrder)
                                    <div style="height: 10px;"></div>
                                    <div class="aui-row" onclick="clickPrize('{{$yxhdOrder->prize_id}}')">
                                        <div class="aui-col-xs-4">
                                            <span>{{$yxhdOrder->prize->name}}</span>
                                        </div>
                                        <div class="aui-col-xs-4">
                                            {{$yxhdOrder->trade_no_str}}
                                        </div>
                                        <div class="aui-col-xs-4">
                                            点击领取
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                    </div>
                </div>
                <img src="{{URL::asset('/img/yxhd/turnplate/html5/left_bg.png')}}" style="width: 100%;margin-top: -5px;"
                     alt=""/>

                <div class="aui-margin-t-15" style="background: #FF715A;">
                    <div class="aui-padded-t-10">
                        <div style="display: inline-block;background: white;width: 80px;height: 26px;line-height: 26px;color: #FF402C;border-radius: 13px;"
                             class="aui-font-size-12">奖品介绍
                        </div>
                        <div style="height: 10px;"></div>
                        <div class="aui-margin-10">
                            {!! $activity->intro_html !!}
                        </div>

                        <div style="height: 30px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div style="height: 30px;"></div>

    <!--分享-->
    <div id="share_mask_div" class="tip_div aui-hide" style="z-index: 200;">
        <!--遮罩层-->
        <div class="mask_div"></div>
        <!--提示部分-->
        <div style="position: absolute;top: 0px;width: 100%;" onclick="closeMask()">
            <img src="{{URL::asset('/img/yxhd/turnplate/html5/share.png')}}" width="284" height="470.5"
                 style="position: absolute;right: -30px;left: 0px;margin: auto;top: 40px">
            <div style="width:284px;height:470.5px;position: absolute;right: -30px;left: 0px;margin: auto;top: 40px">
                <img src="{{URL::asset('/img/yxhd/turnplate/html5/close_btn.png')}}" width="12" height="12"
                     style="margin-top: 160px;float: right;margin-right: 50px"
                     onclick="closeMask()">
            </div>
        </div>
    </div>


    <!--获得奖品-->
    <div id="zhongjiang_mask_div" class="tip_div aui-hide" style="z-index: 200;">
        <!--遮罩层-->
        <div class="mask_div"></div>
        <!--提示部分-->
        <div style="position: absolute;top: 0px;width: 100%;" onclick="closeMask()">
            <img src="{{URL::asset('/img/yxhd/turnplate/html5/zhongjiang_bg.png')}}" width="320" height="300"
                 style="position: relative;margin: auto;top: 140px">
            <div style="width: 100%;position: relative;top: -80px;font-size: 20px;font-weight: bolder;"
                 class="aui-text-white">
                恭喜抽得
            </div>
            <!--奖品描述-->
            <div id="prize_name" style="width: 100%;position: relative;top: -65px;font-size: 26px;font-weight: bolder;"
                 class="aui-text-white">

            </div>
            <div style="width: 100%;position: relative;top: -55px;font-size: 14px;font-weight: bolder;"
                 class="aui-text-white">
                （分享可以获得抽奖机会）
            </div>
            <div style="width: 100%;position: relative;top: -30px;font-size: 14px;font-weight: bolder;"
                 class="aui-text-center">
                <div style="display: inline-block;width: 140px;height: 36px;background: #FEEE8E;line-height: 36px;color: #F3363F;border-radius: 18px;"
                     class="aui-font-size-14" onclick="clickPrize();">查看奖品
                </div>
            </div>
            <img src="{{URL::asset('/img/yxhd/turnplate/html5/close_btn.png')}}" width="12" height="12"
                 style="position: absolute;top: 120px;right: 40px;"
                 onclick="closeMask()">
        </div>
    </div>


    <!--未获得奖品-->
    <div id="meizhongjiang_mask_div" class="tip_div aui-hide" style="z-index: 200;">
        <!--遮罩层-->
        <div class="mask_div"></div>
        <!--提示部分-->
        <div style="position: absolute;top: 0px;width: 100%;" onclick="closeMask()">
            <img src="{{URL::asset('/img/yxhd/turnplate/html5/meizhongjiang_bg.png')}}" width="320" height="300"
                 style="position: relative;margin: auto;top: 140px">
            <div style="width: 100%;position: relative;top: -80px;font-size: 20px;font-weight: bolder;"
                 class="aui-text-white">
                继续加油
            </div>
            <!--奖品描述-->
            <div style="width: 100%;position: relative;top: -65px;font-size: 26px;font-weight: bolder;"
                 class="aui-text-white">
                再接再厉
            </div>
            <div style="width: 100%;position: relative;top: -55px;font-size: 14px;font-weight: bolder;"
                 class="aui-text-white">
                （分享可以获得抽奖机会）
            </div>
            <div style="width: 100%;position: relative;top: -30px;font-size: 14px;font-weight: bolder;"
                 class="aui-text-center">
                <div style="display: inline-block;width: 140px;height: 36px;background: #FEEE8E;line-height: 36px;color: #F3363F;border-radius: 18px;"
                     class="aui-font-size-14" onclick="closeMask();">继续抽奖
                </div>
            </div>
            <img src="{{URL::asset('/img/yxhd/turnplate/html5/close_btn.png')}}" width="12" height="12"
                 style="position: absolute;top: 120px;right: 40px;"
                 onclick="closeMask()">
        </div>
    </div>

@endsection

@section('script')

    <script type="text/javascript" src="{{ URL::asset('/js/yxhd/turnplate/html5/awardRotate.js') }}"></script>

    <script type="text/javascript">

        //大转盘
        var turnplate = {
            restaraunts: [],				//大转盘奖品名称
            colors: [],					//大转盘奖品区块对应背景颜色
            outsideRadius: 152,			//大转盘外圆的半径192
            textRadius: 120,				//大转盘奖品位置距离圆心的距离155
            insideRadius: 38,			//大转盘内圆的半径
            startAngle: 0,				//开始角度

            bRotate: false				//false:停止;ture:旋转
        };

        //初始化数据
        var prizeName_arr = "{{$prizeConfig['name_arr']}}";
        prizeName_arr = prizeName_arr.split(',');
        var prizeId_arr = "{{$prizeConfig['id_arr']}}";
        prizeId_arr = prizeId_arr.split(',');

        var user_id = '{{$user->id}}';
        var activity_id = '{{$activity->id}}';

        var left_turnplate_num = '{{$left_turnplate_num}}';

        //中奖prize_id
        var obtain_prize_id = null;

        //分享链接
        var share_url = ("{{$activity->share_url}}" == "") ? window.location.href : '{{$activity->share_url}}';

        console.log(share_url);

        //微信相关/////////////////////////////////////////////////////
        @if(!env('FWH_LOCAL_DEBUG'))

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
                link: share_url,   // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                imgUrl: '{{$activity->share_img}}', // 分享图标
                success: function (ret) {
                    // 用户确认分享后执行的回调函数
                    console.log("success ret:" + JSON.stringify(ret))
                    shareActivity();
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });

            //app分享
            wx.onMenuShareAppMessage({
                title: '{{$activity->share_title}}', // 分享标题
                desc: '{{$activity->share_desc}}',
                link: share_url, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                imgUrl: '{{$activity->share_img}}', // 分享图标
                type: 'link', // 分享类型,music、video或link，不填默认为link
                dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                success: function (ret) {
                    // 用户确认分享后执行的回调函数
                    console.log("success ret:" + JSON.stringify(ret))
                    shareActivity();
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });
        });

        @endif
        ///////////////////////////////////////////////////////////////


        $(document).ready(function () {

            //动态添加大转盘的奖品与奖品区域背景颜色
            turnplate.restaraunts = prizeName_arr;
            turnplate.colors = ["#FFF4D6", "#FFFFFF", "#FFF4D6", "#FFFFFF", "#FFF4D6", "#FFFFFF", "#FFF4D6", "#FFFFFF", "#FFF4D6", "#FFFFFF"];
            drawRouletteWheel();

            var rotateTimeOut = function () {
                $('#wheelcanvas').rotate({
                    angle: 0,
                    animateTo: 2160,
                    duration: 8000,
                    callback: function () {
                        alert('网络超时，请检查您的网络设置！');
                    }
                });
            };

            //旋转转盘 item:奖品位置; txt：提示语;
            var rotateFn = function (item, txt, prize_id) {
                var angles = item * (360 / turnplate.restaraunts.length) - (360 / (turnplate.restaraunts.length * 2));
                if (angles < 270) {
                    angles = 270 - angles;
                } else {
                    angles = 360 - angles + 270;
                }
                $('#wheelcanvas').stopRotate();
                $('#wheelcanvas').rotate({
                    angle: 0,
                    animateTo: angles + 1800,
                    duration: 8000,
                    callback: function () {
                        turnplate.bRotate = !turnplate.bRotate;
                        //如果未抽中奖品
                        if (prize_id == -1) {
                            $("#meizhongjiang_mask_div").removeClass('aui-hide');
                        } else {
                            $("#prize_name").text(prizeName_arr[item]);
                            $("#zhongjiang_mask_div").removeClass('aui-hide');
                        }
                    }
                });
            };

            $('.pointer').click(function () {
                //是否有抽奖机会
                if (left_turnplate_num <= 0) {
                    clickShare();
                    return;
                }
                //如果转盘在转动
                if (turnplate.bRotate) return;
                turnplate.bRotate = !turnplate.bRotate;

                //使用转盘先转动
                $('#wheelcanvas').rotate({
                    angle: 0,
                    animateTo: 0 + 1800,
                    duration: 8000,
                    callback: function () {
                        turnplate.bRotate = !turnplate.bRotate;
                    }
                });

                //通过接口获取中奖数据
                var param = {
                    user_id: user_id,
                    activity_id: activity_id,
                    _token: '{{ csrf_token() }}'
                }
                ajaxRequest("{{URL::asset('')}}" + "yxhd/api/turnplate/draw", param, "post", function (ret) {
                    console.log("ret:" + JSON.stringify(ret));
                    //提交成功
                    if (ret.result == true) {
                        var prize_id = ret.ret;
                        obtain_prize_id = prize_id;     //2018-12-28 中奖的奖品id，用于弹出中奖页面，点击查看奖品进入奖品说明页面
                        var prize_index = getIndexById_inIdArr(prize_id);
                        console.log("prize_index:" + prize_index);
                        rotateFn(prize_index, turnplate.restaraunts[prize_index], prize_id);

                    } else {
                        alert(ret.message);
                    }
                });
            });
        });


        //进行分享
        function shareActivity() {
            var param = {
                user_id: user_id,
                activity_id: activity_id,
                _token: '{{ csrf_token() }}'
            }
            ajaxRequest("{{URL::asset('')}}" + "yxhd/api/turnplate/share", param, "post", function (ret) {
                console.log("ret:" + JSON.stringify(ret));
                //提交成功
                if (ret.result == true) {
                    //刷新页面
                    location.reload();
                } else {
                    alert(ret.message);
                }
            });
        }

        //根据id返回数组中的数据
        function getIndexById_inIdArr(id) {
            var index_arr = [];
            for (var i = 0; i < prizeId_arr.length; i++) {
                if (prizeId_arr[i] == id) {
                    index_arr.push(i);      //将索引放置到数组中
                }
            }
            if (index_arr.length == 0) {
                return prizeId_arr[prizeId_arr.length - 1];     //默认最后一个是谢谢参与
            } else {
                return index_arr[rnd(0, index_arr.length - 1)];     //返回中奖数的某一个
            }
        }

        //获得随机数
        function rnd(n, m) {
            var random = Math.floor(Math.random() * (m - n + 1) + n);
            return random;
        }

        //绘制抽奖轮盘
        function drawRouletteWheel() {
            var canvas = document.getElementById("wheelcanvas");
            if (canvas.getContext) {
                //根据奖品个数计算圆周角度
                var arc = Math.PI / (turnplate.restaraunts.length / 2);
                var ctx = canvas.getContext("2d");
                //在给定矩形内清空一个矩形
                ctx.clearRect(0, 0, 380, 380);
                //strokeStyle 属性设置或返回用于笔触的颜色、渐变或模式
                ctx.strokeStyle = "#FFBE04";
                //font 属性设置或返回画布上文本内容的当前字体属性
                ctx.font = '12px Microsoft YaHei';
                for (var i = 0; i < turnplate.restaraunts.length; i++) {
                    var angle = turnplate.startAngle + i * arc;
                    ctx.fillStyle = turnplate.colors[i];
                    ctx.beginPath();
                    //arc(x,y,r,起始角,结束角,绘制方向) 方法创建弧/曲线（用于创建圆或部分圆）
//		  alert(angle)
                    ctx.arc(190, 190, turnplate.outsideRadius, angle, angle + arc, false);
                    ctx.arc(190, 190, turnplate.insideRadius, angle + arc, angle, true);
                    ctx.stroke();
                    ctx.fill();
                    //锁画布(为了保存之前的画布状态)
                    ctx.save();

                    //----绘制奖品开始----
                    ctx.fillStyle = "#E5302F";
                    var text = turnplate.restaraunts[i];
                    var line_height = 17;
                    //translate方法重新映射画布上的 (0,0) 位置
                    ctx.translate(190 + Math.cos(angle + arc / 2) * turnplate.textRadius, 190 + Math.sin(angle + arc / 2) * turnplate.textRadius);

                    //rotate方法旋转当前的绘图
                    ctx.rotate(angle + arc / 2 + Math.PI / 2);

                    /** 下面代码根据奖品类型、奖品名称长度渲染不同效果，如字体、颜色、图片效果。(具体根据实际情况改变) **/
                    if (text.indexOf("元") > 0) {//流量包
                        var texts = text.split("元");
                        for (var j = 0; j < texts.length; j++) {
                            ctx.font = j == 0 ? 'bold 20px Microsoft YaHei' : '12px Microsoft YaHei';
                            if (j == 0) {
                                ctx.fillText(texts[j] + "元", -ctx.measureText(texts[j] + "元").width / 2, j * line_height);
                            } else {
                                ctx.fillText(texts[j], -ctx.measureText(texts[j]).width / 2, j * line_height);
                            }
                        }
                    } else if (text.indexOf("元") == -1 && text.length > 6) {//奖品名称长度超过一定范围
                        text = text.substring(0, 6) + "||" + text.substring(6);
                        var texts = text.split("||");
                        for (var j = 0; j < texts.length; j++) {
                            ctx.fillText(texts[j], -ctx.measureText(texts[j]).width / 2, j * line_height);
                        }
                    } else {
                        //在画布上绘制填色的文本。文本的默认颜色是黑色
                        //measureText()方法返回包含一个对象，该对象包含以像素计的指定字体宽度
                        ctx.fillText(text, -ctx.measureText(text).width / 2, 0);
                    }

                    //把当前画布返回（调整）到上一个save()状态之前
                    ctx.restore();
                    //----绘制奖品结束----
                }
            }
        }


        //继续抽奖
        function clickShare() {
            $("#share_mask_div").removeClass('aui-hide')
            javascript:document.getElementById('2F').scrollIntoView()
        }


        //关闭全部的mask_div
        function closeMask() {
            $("#share_mask_div").addClass('aui-hide')
            $("#zhongjiang_mask_div").addClass('aui-hide')
            $("#meizhongjiang_mask_div").addClass('aui-hide')
            //刷新页面
            location.reload();
        }

        //跳转至奖品页面
        function clickPrize(prize_id) {
            console.log("clickPrize prize_id:" + prize_id);
            toast_loading("奖品详情...");
            if (judgeIsAnyNullStr(prize_id)) {
                prize_id = obtain_prize_id; //赋值为中奖页面
            }
            window.location.href = "{{URL::asset('/yxhd/turnplate/prize')}}?prize_id=" + prize_id;
            event.stopPropagation();
        }

    </script>
@endsection