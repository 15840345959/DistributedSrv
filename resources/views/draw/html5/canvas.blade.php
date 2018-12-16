@extends('draw.html5.layouts.app')

@section('content')

    <style type="text/css">
        html, body {
            background: #f1f1f1;
        }

        /*小图标样式*/
        .icon-style {
            width: 26px;
            height: 26px;
        }

        /*滑块*/
        .aui-range {
            position: relative;
            display: inline-block;
        }

        /*横条样式*/
        .aui-range input[type='range'] {
            height: 0.2rem;
            border: 0;
            border-radius: 2px;
            background: -webkit-linear-gradient(#03a9f4, #03a9f4) no-repeat, #FFFFFF;
            background-size: 20% 100%; /*设置左右宽度比例*/
            position: relative;
            -webkit-appearance: none !important;
        }

        /*拖动块样式*/
        .aui-range input[type='range']::-webkit-slider-thumb {
            width: 1.0rem;
            height: 1.0rem;
            border: 6px solid #FFFFFF;
            border-radius: 50%;
            -webkit-appearance: none !important;
        }

        .aui-range .aui-range-tip {
            font-size: 1rem;
            position: absolute;
            z-index: 999;
            top: -1.5rem;
            width: 2.4rem;
            height: 1.5rem;
            line-height: 1.5rem;
            text-align: center;
            color: #666666;
            border: 1px solid #dddddd;
            border-radius: 0.3rem;
            background-color: #ffffff;
        }

        .aui-input-row .aui-range input[type='range'] {
            width: 90%;
            margin-left: 5%;
        }

        /*滑动门*/
        ::-webkit-scrollbar {
            display: none;
        }

        .scroll-item {
            width: 24px;
            height: 40px;
            text-align: center;
            display: inline-block;
            overflow: hidden
        }

        .scroll-wrap {
            width: 100%;
            -webkit-overflow-scrolling: touch;
            overflow-y: hidden;
            overflow-x: auto;
            white-space: nowrap;
        }

        /*遮罩层*/
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

    <title>简笔画板</title>

    <!--顶部header-->
    <header class="aui-bar aui-bar-nav" style="position:fixed;top: 0px;width: 100%;z-index: 100;background: yellow;">
        <a class="aui-pull-left">
            <span class="aui-iconfont aui-icon-left aui-text-default"></span>
        </a>

        <div class="aui-title aui-text-default">每天一画</div>
    </header>
    <div style="height: 2.25rem;"></div>

    <!--画布-->
    <div style="width: 100%;height: 320px;background: white;">
        <canvas id="canvas" class="draw_canvas" style="width: 100%;height: 320px;"></canvas>
    </div>

    </div>
    <!--小工具-->
    <div class="aui-margin-t-10">
        <div class="aui-row" style="height: 40px;">
            <!--线条配置-->
            <span style="height: 32px;line-height: 32px;"
                  class="aui-pull-left aui-margin-l-15 text-grey-999 aui-font-size-14">粗细
            <div id="pen_width_span" style="width: 14px;display: inline-block;">1</div></span>

            <div class="aui-range aui-margin-l-10">
                <input type="range" class="aui-range" value="3" max="25" min="1" data-attr="#336677"
                       step="1" id="pen_width_range" oninput="changePenWidth();">
            </div>
            <!--小工具-->
            <span class="aui-pull-right">
             <img src="{{URL::asset('/img/del.png')}}" class="aui-margin-r-15 icon-style" onclick="clickDel();">
        </span>
            <span class="aui-pull-right">
             <img src="{{URL::asset('/img/eraser.png')}}" class="aui-margin-r-15 icon-style" onclick="clickEraser();">
        </span>
            <span class="aui-pull-right">
             <img src="{{URL::asset('/img/undo.png')}}" class="aui-margin-r-15 icon-style" onclick="clickUndo();">
        </span>
        </div>
        <div class="aui-margin-l-15 aui-margin-t-5">
            <div id="color-board-content" class="aui-padded-r-15 aui-padded-r-15 scroll-wrap" style="height: 50px;">

            </div>
        </div>

        <script id="color-board-content-template" type="text/x-dot-template">
            <div class=" aui-margin-r-15 scroll-item">
                <div style="top: 0px;">
                    <div style="width: 16px;height: 16px;background:@{{=it.value}};display: inline-block;"
                         onclick="changeColor('@{{=it.index}}');"></div>
                </div>
                <div style="margin-top: -10px;">
                    <div id="color_bottom_tip@{{=it.index}}"
                         style="width: 16px;height: 2px;background:@{{=it.value}};display: inline-block;"
                         class="aui-hide color_bottom_tip"></div>
                </div>
            </div>
        </script>

        <!--保存按钮-->
        <div class="aui-flex-middle aui-flex-col aui-flex-center">
            <div style="width: 50%;background: yellow;height: 40px;line-height: 40px;border-radius: 5px;"
                 class="aui-text-center aui-font-size-16"
                 onclick="clickSave();">保存图片
            </div>
            <div style="width: 40px;height: 40px;display: inline-block;background: yellow;border-radius: 5px;"
                 class="aui-margin-l-15">
                <img src="{{URL::asset('/img/replay_btn.png')}}" style="width: 30px;height: 30px;"
                     class="aui-margin-l-5 aui-margin-t-5"
                     onclick="clickReplay();">
            </div>
        </div>

        <!--重新播放的遮罩层-->
        <div id="replay_div" class="tip_div aui-hide">
            <!--遮罩层-->
            <div class="mask_div"></div>
            <div style="position: absolute;top: 100px;width: 100%;">
                <div style="background: white;">
                    <canvas id="replay_canvas" style="width: 100%;height: 320px;"></canvas>
                </div>
            </div>
            <div class="aui-flex-col aui-flex-middle aui-flex-center"
                 style="position: absolute;top: 440px;width: 100%;">
                <img src="{{URL::asset('/img/close_white_btn.png')}}" style="width: 40px;height: 40px;"
                     onclick="closeReplayTip();">
                <img src="{{URL::asset('/img/replay_white_btn.png')}}"
                     style="width: 44px;height: 44px;margin-left: 60px;"
                     onclick="clickReplay();">
            </div>
        </div>

        {{--作品编辑页面--}}


    </div>

@endsection

@section('script')

    <script type="text/javascript" src="{{ URL::asset('/js/qiniu.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('/js/plupload/plupload.full.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('/js/plupload/moxie.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('/js/doT.min.js') }}"></script>

    <script>

    </script>

    <script type="text/javascript">
        //颜色色卡
        var color_borad_vals = [
            {name: 'color_1', value: "#050505"}, {name: 'color_1', value: "#FFFFFF"}, {
                name: 'color_1',
                value: "#CDCDCD"
            }, {
                name: 'color_2',
                value: "#FF7F50"
            }, {name: 'color_3', value: "#FF83FA"}
            , {name: 'color_4', value: "#B452CD"}, {name: 'color_5', value: "#8B8B00"}, {
                name: 'color_6',
                value: "#708090"
            }
            , {name: 'color_7', value: "#CAFF70"}, {name: 'color_8', value: "#FF3030"}, {
                name: 'color_9',
                value: "#CD8162"
            }
            , {name: 'color_10', value: "#FFBB32"}, {name: 'color_11', value: "#0198CF"}, {
                name: 'color_12',
                value: "#FF00FF"
            }
        ];

        //画布快照堆栈，用于undo
        var cPhoto_arr = [];

        //操作步骤堆栈，用于记录创作过程
        var steps_arr = [];

        //当前颜色
        var curr_pen_color = "#B452CD";     //当前颜色
        var curr_pen_width = 5; //前端画笔宽度

        var canvas = null;  //画布
        var ctx = null;   //画布Context

        var painting_flag = false;      //是否在画画的标识

        //入口函数
        $(document).ready(function () {

            //取消页面弹动
            document.body.addEventListener('touchmove', function (e) {
                if (!document.querySelector('.scroll-wrap').contains(e.target) && !document.querySelector('#pen_width_range').contains(e.target)) {
                    console.log("prepare e.preventDefault");
                    e.preventDefault();
                }
            }, {passive: false});

            //页面属性
            var pageWidth = document.documentElement.clientWidth;
            var pageHeight = document.documentElement.clientHeight;
            console.log("pageWidth:" + pageWidth + " pageHeight:" + pageHeight);
            //初始化画布
            canvas = document.getElementById("canvas");
            canvas.width = pageWidth;
            canvas.height = 320;
            console.log("画布偏移:" + "left:" + canvas.offsetTop + "vtop:" + canvas.offsetTop);
            ctx = canvas.getContext("2d");
            ctx.lineCap = "round";
            //初始化颜色板
            init_colorBoard();
            //触摸事件开始
            canvas.addEventListener('touchstart', function (event) {//触摸点按下事件
                var touch = event.targetTouches[0];
                console.log("touchstart event:" + JSON.stringify(event) + " clientX:" + touch.clientX + " clientY:" + touch.clientY);
                touchStartFun(touch);       //开始
            }, false);

            //监听移动
            canvas.addEventListener('touchmove', function (event) {//触摸点按下事件
                var touch = event.targetTouches[0];
                console.log("touchmove event:" + JSON.stringify(event) + " clientX:" + touch.clientX + " clientY:" + touch.clientY
                    + " canvas offsetLeft:" + canvas.offsetLeft + " canvas.offsetTop:" + canvas.offsetTop);
                touchMoveFun(touch);     //移动
            }, false);

            //监听触摸结束
            canvas.addEventListener('touchend', function (event) {//触摸点按下事件
                var touch = event.targetTouches[0];
                console.log("touchend event:" + JSON.stringify(event));
                touchEndFun(touch);
            }, false);

        });


        //初始化画板
        function init_colorBoard() {
            for (var i = 0; i < color_borad_vals.length; i++) {
                console.log("i:" + i + " color_board_vals[i]:" + JSON.stringify(color_borad_vals[i]));
                var color_obj = color_borad_vals[i];
                color_obj.index = i;
                var interText = doT.template($("#color-board-content-template").text());
                $("#color-board-content").append(interText(color_borad_vals[i]));
            }
            changeColor(0);
            changePenWidth();
        }

        //修改画笔宽度
        function changePenWidth() {
            curr_pen_width = $("#pen_width_range").val();
            $("#pen_width_span").text(curr_pen_width);
            console.log("curr_pen_width:" + curr_pen_width);
            //配置颜色和宽度
            $("#pen_width_range").css("background-size", curr_pen_width * 100 / 25 + "% 100%");
            //设置画笔宽度
            ctx.lineWidth = curr_pen_width;

        }

        //点击颜色版
        function changeColor(index) {
            console.log("changeColor index:" + index);
            curr_pen_color = color_borad_vals[index].value;
            //更改横条颜色
            $("#pen_width_range").css("background", "-webkit-linear-gradient(" + curr_pen_color + ", " + curr_pen_color + ") no-repeat, #FFFFFF");
            changePenWidth();
            ctx.strokeStyle = curr_pen_color;       //设置笔触颜色
            ctx.fillStyle = curr_pen_color;     //设置填充颜色

            $(".color_bottom_tip").addClass('aui-hide');
            $("#color_bottom_tip" + index).removeClass('aui-hide');
        }

        //点击删除
        function clickDel() {
            var dialog = new auiDialog();
            dialog.alert({
                title: "清空画布",
                msg: '是否确认清空画布，清空画布后您的绘画轨迹将丢失',
                buttons: ['取消', '确定']
            }, function (ret) {
                console.log(ret)
                if (ret.buttonIndex == 2) {
                    console.log("清理画布 canvas.width:" + canvas.width + " canvas.height:" + canvas.height);
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    steps_arr = [];
                }
            })
        }

        //点击橡皮擦
        function clickEraser() {
            changeColor(1);
        }

        //点击undo
        function clickUndo() {
            console.log("clickUndo cPhoto_arr.length:" + cPhoto_arr.length);
            //堆栈数组大于0
            if (cPhoto_arr.length > 0) {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                var pic = new Image();
                var lastPic = cPhoto_arr.pop();
                console.log("clickUndo lastPic:" + lastPic);
                pic.src = lastPic;
                pic.onload = function () {
                    ctx.drawImage(pic, 0, 0);
                }
                steps_arr.push({
                    action: 'undo'
                });
            }
        }

        //touch start功能，秒绘一个点，启动path，move to一个点
        function touchStartFun(touch) {
            //将信息推入堆栈
            cPhoto_arr.push(canvas.toDataURL('image/png'));
            steps_arr.push({action: 'cPhoto'});      //存储快照
            //标点+建立路径
            ctx.fillRect(touch.clientX - canvas.offsetLeft - (curr_pen_width / 2), touch.clientY - canvas.offsetTop - (curr_pen_width / 2), curr_pen_width / 2, curr_pen_width / 2);
            steps_arr.push({
                action: 'fillRect',
                option: {color: curr_pen_color, width: curr_pen_width, x: touch.clientX, y: touch.clientY}
            });
            ctx.beginPath();
            steps_arr.push({
                action: 'beginPath'
            });
            ctx.moveTo(touch.clientX - canvas.offsetLeft, touch.clientY - canvas.offsetTop);
            steps_arr.push({
                action: 'moveTo',
                option: {x: touch.clientX, y: touch.clientY}
            });
        }

        //touch move功能，描绘线条
        function touchMoveFun(touch) {
            ctx.lineTo(touch.clientX - canvas.offsetLeft, touch.clientY - canvas.offsetTop);
            steps_arr.push({
                action: 'lineTo',
                option: {color: curr_pen_color, width: curr_pen_width, x: touch.clientX, y: touch.clientY}
            });
            ctx.stroke();
            steps_arr.push({
                action: 'stroke',
            });
        }

        //touch end功能，关闭路径
        function touchEndFun(touch) {
            ctx.closePath();
            steps_arr.push({
                action: 'closePath'
            });
            console.log("touchEndFun cPhoto_arr.length:" + cPhoto_arr.length);
        }

        //点击保存图片
        function clickSave() {
            console.log("clickSave steps_arr:" + JSON.stringify(steps_arr));
            if (steps_arr.length == 0) {
                var dialog = new auiDialog();
                dialog.alert({
                    title: "提示信息",
                    msg: '您还没有开始绘画，无法保存',
                    buttons: ['确定']
                }, function (ret) {
                    console.log(ret)
                })
                return;
            }

            //进行保存

        }

        //replay相关
        var replay_task = null;        //重播任务
        var replay_canvas = null;       //重播画布
        var replay_ctx = null;              //重播上下文

        //重播堆栈
        var replay_cPhoto_arr = [];


        //点击播放
        function clickReplay() {
            console.log("clickSave steps_arr:" + JSON.stringify(steps_arr));
            if (steps_arr.length == 0) {
                var dialog = new auiDialog();
                dialog.alert({
                    title: "提示信息",
                    msg: '您还没有开始绘画，无法播放录像',
                    buttons: ['确定']
                }, function (ret) {
                    console.log(ret)
                })
                return;
            }

            $("#replay_div").removeClass("aui-hide");

            //页面属性
            var pageWidth = document.documentElement.clientWidth;
            var pageHeight = document.documentElement.clientHeight;
            console.log("pageWidth:" + pageWidth + " pageHeight:" + pageHeight);
            //初始化画布
            replay_canvas = document.getElementById("replay_canvas");
            replay_canvas.width = pageWidth;
            replay_canvas.height = 320;
            console.log("画布偏移:" + "left:" + replay_canvas.offsetLeft + "vtop:" + canvas.offsetTop);
            replay_ctx = replay_canvas.getContext("2d");
            replay_ctx.lineCap = "round";

            replay_step_count = 0;
            replay_ctx.clearRect(0, 0, replay_canvas.width, replay_canvas.height);       //清空画布

            clearInterval(replay_task);     //关闭现存的定时器任务

            replay_task = setInterval(replay, 30);      //启动任务
        }

        var replay_step_count = 0;     //步骤信息

        //重放任务
        function replay() {
            if (steps_arr.length == replay_step_count) {
                clearInterval(replay_task);
            } else {
                handleStep(steps_arr[replay_step_count++]);
            }
        }

        //处理每一步动画
        function handleStep(step_obj) {
            console.log("handleStep step_obj:" + JSON.stringify(step_obj));
            //分情况进行处理
            switch (step_obj.action) {
                case "cPhoto":
                    replay_cPhoto_arr.push(replay_canvas.toDataURL('image/png'));
                    break;
                case "fillRect":
                    console.log("handleStep fillRect");
                    replay_ctx.strokeStyle = step_obj.option.color;       //设置笔触颜色
                    replay_ctx.fillStyle = step_obj.option.color;     //设置填充颜色
                    replay_ctx.fillRect(step_obj.option.x - replay_canvas.offsetLeft - (step_obj.option.width / 2), step_obj.option.y - canvas.offsetTop - (step_obj.option.width / 2), step_obj.option.width / 2, step_obj.option.width / 2);
                    break;
                case "beginPath":
                    replay_ctx.beginPath();
                    break;
                case "moveTo":
                    replay_ctx.moveTo(step_obj.option.x - replay_canvas.offsetLeft, step_obj.option.y - canvas.offsetTop);
                    break;
                case "lineTo":
                    replay_ctx.strokeStyle = step_obj.option.color;       //设置笔触颜色
                    replay_ctx.fillStyle = step_obj.option.color;     //设置填充颜色
                    replay_ctx.lineWidth = step_obj.option.width;
                    replay_ctx.lineTo(step_obj.option.x - replay_canvas.offsetLeft, step_obj.option.y - canvas.offsetTop);
                    break;
                case "stroke":
                    replay_ctx.stroke();
                    break;
                case "closePath":
                    replay_ctx.closePath();
                    break;
                case "undo":
                    setUndo();
                    break;
            }
        }


        //点击关闭replaydiv
        function closeReplayTip() {
            $("#replay_div").addClass("aui-hide");
            clearInterval(replay_task);
        }


        //点击undo-用于重播
        function setUndo() {
            console.log("clickUndo replay_cPhoto_arr.length:" + replay_cPhoto_arr.length);
            //堆栈数组大于0
            if (replay_cPhoto_arr.length > 0) {
                replay_ctx.clearRect(0, 0, replay_canvas.width, replay_canvas.height);
                var pic = new Image();
                var lastPic = replay_cPhoto_arr.pop();
                console.log("clickUndo lastPic:" + lastPic);
                pic.src = lastPic;
                pic.onload = function () {
                    replay_ctx.drawImage(pic, 0, 0);
                }
            }
        }

    </script>
@endsection