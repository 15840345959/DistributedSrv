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

    <title>报名信息</title>

    <div style="background: white;">
        <div class="aui-padded-t-15">
            <span class="aui-font-size-16 aui-margin-l-15" style="font-weight: bolder;">欢迎您的报名</span>
        </div>
        <ul class="aui-list aui-form-list aui-margin-l-10 aui-font-size-14 aui-margin-t-10 aui-list-noborder"
            style="border: 0px;background: white;">
            <li class="aui-list-item" style="border: none !important;background: white;">
                <div class="aui-list-item-label-icon">
                    <img src="{{URL::asset('/img/vote_user.jpg')}}" style="width: 16px;height: 16px;">
                </div>
                <div class="aui-list-item-inner" style="">
                    <input id="name" name="name" type="text" placeholder="请输入名字" class="aui-input aui-font-size-14"
                           value="">
                </div>
            </li>
            <li class="aui-list-item" style="border: none !important;background: white;">
                <div class="aui-list-item-label-icon">
                    <img src="{{URL::asset('/img/vote_phonenum.png')}}"
                         style="width: 16px;height: 16px;">
                </div>
                <div class="aui-list-item-inner" style="">
                    <input id="phonenum" name="phonenum" type="text" placeholder="请输入手机号"
                           class="aui-input aui-font-size-14" value="{{$user->phonenum?$user->phonenum:''}}">
                </div>
            </li>
            <li class="aui-list-item" style="border: none !important;background: white;">
                <div class="aui-list-item-label-icon">
                    <img src="{{URL::asset('/img/vote_xuanyan.png')}}"
                         style="width: 16px;height: 16px;">
                </div>
                <div class="aui-list-item-inner" style="">
                    <input id="declaration" name="declaration" type="text" placeholder="{{$activity->apply_info_1}}"
                           class="aui-input aui-font-size-14">
                </div>
            </li>
            <li class="aui-list-item" style="border: none !important;background: white;">
                <div class="aui-list-item-label-icon">
                    <img src="{{URL::asset('/img/vote_zuopin.png')}}" style="width: 16px;height: 16px;">
                </div>
                <div class="aui-list-item-inner" style="">
                    <input id="work_name" name="work_name" type="text" placeholder="{{$activity->apply_info_2}}"
                           class="aui-input aui-font-size-14">
                </div>
            </li>
            <li class="aui-list-item" style="border: none !important;">
                <div class="aui-list-item-label-icon">
                    <img src="{{URL::asset('/img/vote_intro.png')}}" style="width: 16px;height: 16px;">
                </div>
                <div class="aui-list-item-inner" style="">
                    <input id="work_desc" name="work_desc" type="text" placeholder="{{$activity->apply_info_3}}"
                           class="aui-input aui-font-size-14">
                </div>
            </li>
        </ul>
        <div class="aui-margin-15">
            <div class="text-grey-999 aui-font-size-12 aui-padded-l-10">
                请选择上传1-5张图片，第一张作为封面图
            </div>
            <div class="aui-row aui-padded-b-15 aui-margin-t-5">
                <div id="works-content">

                </div>
                <div id="container" class="aui-col-xs-4">
                    <img id="pickfiles" src="{{URL::asset('/img/add_pic.png')}}" class="aui-padded-10">
                </div>
            </div>
        </div>
        {{--作品列表--}}
        <script id="works-content-template" type="text/x-dot-template">
            @{{for(var i=0;i
            <it.length ;i++){}}
            <div id="work_@{{=i}}" class="aui-col-xs-4" style="position: relative;">
                <img src="{{URL::asset('/img/close_full_red_btn.png')}}"
                     style="position: absolute;top: 5px;left: 5px;height: 16px;width: 16px;"
                     onclick="clickDelImg(@{{=i}})">
                <img src="@{{=it[i]}}?imageView2/1/w/350/h/350/interlace/1/q/75" class="aui-padded-10">
            </div>
            @{{?}}
        </script>

        <div class="aui-text-center aui-margin-t-15">
            <span class="aui-font-size-12 text-grey-999">请如实填写报名信息，获取参赛资格</span>
        </div>
        <div class="aui-text-center aui-padded-t-15 aui-padded-b-15" onclick="clickSave();">
            <span class="aui-btn" style="height: 36px;line-height: 36px;width: 140px;">提交报名</span>
        </div>
        <div class="aui-margin-t-5 aui-text-center">
            <span class="main-color aui-font-size-14" onclick="clickJGApply({{$activity->id}});">机构报名</span>
        </div>
    </div>

    <!--报名成功的提示-->
    <div id="apply_success_div" class="tip_div aui-hide" style="z-index: 999;">
        <!--遮罩层-->
        <div class="mask_div"></div>
        <!--提示部分-->
        <div style="position: absolute;top: 120px;width: 100%;">
            <div style="width: 70%;margin: auto;">
                <div class="aui-text-center"
                     style="background: #FF5959;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <div>
                        <img src="{{URL::asset('/img/close_white_btn.png')}}" class="aui-padded-l-10 aui-padded-t-10"
                             style="width: 30px;height: 30px;" onclick="closeSuccessTip();">
                    </div>
                    <div style="height: 10px;"></div>
                    <div>
                    <span style="margin-top: 30px;font-weight: bolder;"
                          class="aui-font-size-16 aui-text-white">报名成功</span>
                    </div>
                    <div class="aui-padded-t-15">
                    <span style="margin-top: 30px;"
                          class="aui-font-size-14 aui-text-white">大赛组委会将在一个工作日内审核</span>
                    </div>
                    <div style="height: 30px;"></div>
                </div>
                <div class="aui-text-center"
                     style="background: white;border-bottom-left-radius: 5px;border-bottom-right-radius: 5px;">
                    <div style="padding-top: 30px;">
                        <img src="{{URL::asset('/img/isart_fwh.jpg')}}"
                             style="width: 120px;height: 120px;margin: auto;">
                    </div>
                    <div style="padding-top: 20px;padding-bottom: 30px;">
                        <span class="aui-font-size-14" style="color: #FF5959;">扫码接收审核信息</span>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!--重复报名提示-->
    <div id="already_apply_div" class="tip_div aui-hide" style="z-index: 999;">
        <!--遮罩层-->
        <div class="mask_div"></div>
        <!--提示部分-->
        <div style="position: absolute;top: 120px;width: 100%;">
            <div style="width: 70%;margin: auto;">
                <div class="aui-text-center"
                     style="background: #FF5959;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <div>
                        <img src="{{URL::asset('/img/close_white_btn.png')}}" class="aui-padded-l-10 aui-padded-t-10"
                             style="width: 30px;height: 30px;" onclick="closeAlreadyApplyTip();">
                    </div>
                    <div style="height: 10px;"></div>
                    <div>
                    <span style="margin-top: 30px;font-weight: bolder;"
                          class="aui-font-size-16 aui-text-white">您已经报名</span>
                    </div>
                    <div class="aui-padded-t-15">
                    <span style="margin-top: 30px;"
                          class="aui-font-size-14 aui-text-white">大赛组委会将在一个工作日内审核</span>
                    </div>
                    <div style="height: 30px;"></div>
                </div>
                <div class="aui-text-center"
                     style="background: white;border-bottom-left-radius: 5px;border-bottom-right-radius: 5px;">
                    <div style="padding-top: 30px;">
                        <img src="{{URL::asset('/img/isart_fwh.jpg')}}"
                             style="width: 120px;height: 120px;margin: auto;">
                    </div>
                    <div style="padding-top: 20px;padding-bottom: 30px;">
                        <span class="aui-font-size-14" style="color: #FF5959;">扫码接收审核信息</span>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div style="height: 100px;"></div>
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
            <img src="{{URL::asset('/img/suggest_n.png')}}" style="width: 18px;height: 18px;margin: auto;">

            <div class="aui-bar-tab-label aui-font-size-12">投诉</div>
        </div>
    </footer>

@endsection

@section('script')

    <script type="text/javascript" src="{{ URL::asset('/js/aui/aui-slide.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('/js/qiniu.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('/js/plupload/plupload.full.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('/js/plupload/moxie.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('/js/doT.min.js') }}"></script>

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

        var img_arr = [];


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
            //加载作品
            loadWorksHtml();
            //初始化七牛上传
            initQNUploader();
        });

        //初始化七牛上传模块
        function initQNUploader() {
            var uploader = Qiniu.uploader({
                runtimes: 'html5,flash,html4',      // 上传模式，依次退化
                browse_button: 'pickfiles',         // 上传选择的点选按钮，必需
                container: 'container',//上传按钮的上级元素ID
                // 在初始化时，uptoken，uptoken_url，uptoken_func三个参数中必须有一个被设置
                // 切如果提供了多个，其优先级为uptoken > uptoken_url > uptoken_func
                // 其中uptoken是直接提供上传凭证，uptoken_url是提供了获取上传凭证的地址，如果需要定制获取uptoken的过程则可以设置uptoken_func
                uptoken: "{{$upload_token}}", // uptoken是上传凭证，由其他程序生成
                // uptoken_url: '/uptoken',         // Ajax请求uptoken的Url，强烈建议设置（服务端提供）
                // uptoken_func: function(file){    // 在需要获取uptoken时，该方法会被调用
                //    // do something
                //    return uptoken;
                // },
                get_new_uptoken: false,             // 设置上传文件的时候是否每次都重新获取新的uptoken
                // downtoken_url: '/downtoken',
                // Ajax请求downToken的Url，私有空间时使用，JS-SDK将向该地址POST文件的key和domain，服务端返回的JSON必须包含url字段，url值为该文件的下载地址
                unique_names: true,              // 默认false，key为文件名。若开启该选项，JS-SDK会为每个文件自动生成key（文件名）
                // save_key: true,                  // 默认false。若在服务端生成uptoken的上传策略中指定了sava_key，则开启，SDK在前端将不对key进行任何处理
                domain: 'http://twst.isart.me/',     // bucket域名，下载资源时用到，必需
                max_file_size: '100mb',             // 最大文件体积限制
                flash_swf_url: 'path/of/plupload/Moxie.swf',  //引入flash，相对路径
                max_retries: 3,                     // 上传失败最大重试次数
                dragdrop: true,                     // 开启可拖曳上传
                drop_element: 'container',          // 拖曳上传区域元素的ID，拖曳文件或文件夹后可触发上传
                chunk_size: '4mb',                  // 分块上传时，每块的体积
                auto_start: true,                   // 选择文件后自动上传，若关闭需要自己绑定事件触发上传
                //x_vars : {
                //    查看自定义变量
                //    'time' : function(up,file) {
                //        var time = (new Date()).getTime();
                // do something with 'time'
                //        return time;
                //    },
                //    'size' : function(up,file) {
                //        var size = file.size;
                // do something with 'size'
                //        return size;
                //    }
                //},
                init: {
                    'FilesAdded': function (up, files) {
                        plupload.each(files, function (file) {
                            // 文件添加进队列后，处理相关的事情
                            // alert(alert(JSON.stringify(file)));
                        });
                    },
                    'BeforeUpload': function (up, file) {
                        // 每个文件上传前，处理相关的事情
//                        consoledebug.log("BeforeUpload up:" + up + " file:" + JSON.stringify(file));
                        toast_loading("上传中...");
                    },
                    'UploadProgress': function (up, file) {
                        // 每个文件上传时，处理相关的事情
                        consoledebug.log("UploadProgress up:" + up + " file:" + JSON.stringify(file));
                        // toast_loading(file.percent + "%...");
                    },
                    'FileUploaded': function (up, file, info) {
                        // 每个文件上传成功后，处理相关的事情
                        // 其中info是文件上传成功后，服务端返回的json，形式如：
                        // {
                        //    "hash": "Fh8xVqod2MQ1mocfI4S4KpRL6D98",
                        //    "key": "gogopher.jpg"
                        //  }
//                        consoledebug.log(JSON.stringify(info));
                        var domain = up.getOption('domain');
                        var res = JSON.parse(info);
                        //获取上传成功后的文件的Url
                        var sourceLink = domain + res.key;
                        //作品列表
                        img_arr.push(sourceLink);
                        loadWorksHtml();
                        toast_hide();
//                        consoledebug.log($("#pickfiles").attr('src'));
                    },
                    'Error': function (up, err, errTip) {
                        //上传出错时，处理相关的事情
                        consoledebug.log(err + errTip);
                    },
                    'UploadComplete': function () {
                        //队列文件处理完毕后，处理相关的事情
                    },
                    'Key': function (up, file) {
                        // 若想在前端对每个文件的key进行个性化处理，可以配置该函数
                        // 该配置必须要在unique_names: false，save_key: false时才生效

                        var key = "";
                        // do something with key here
                        return key
                    }
                }
            });
        }

        //使用doT加载页面
        function loadWorksHtml() {
            var interText = doT.template($("#works-content-template").text());
            $("#works-content").html(interText(img_arr));
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

        //点击机构报名
        function clickJGApply(activity_id) {
            toast_loading("机构报名...");
            window.location.href = "{{URL::asset('/vote/apply')}}?activity_id=" + activity_id + "&jg_flag=1";
        }

        //点击报名
        function clickSave() {

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
            if (img_arr.length < 1) {
                dialog_show({
                    msg: "最少上传一组作品",
                    buttons: ['确定'],
                }, null);
                return;
            }
            //封装参数
            var param = {
                user_id: '{{$user->id}}',
                activity_id: '{{$activity->id}}',
                name: name,
                phonenum: $("#phonenum").val(),
                declaration: $("#declaration").val(),
                work_name: $("#work_name").val(),
                work_desc: $("#work_desc").val(),
                img: img_arr.toString(),
                type: '1',
                _token: '{{ csrf_token() }}'
            }
            toast_loading("提交中...");
            v1_apply("{{URL::asset('')}}", param, function (ret) {
                console.log("ret:" + JSON.stringify(ret));
                //提交成功
                if (ret.result == true) {
                    $("#apply_success_div").removeClass('aui-hide');
                    toast_loading("跳转个人主页...");
                    window.location.href = "{{URL::asset('/vote/person')}}?vote_user_id=" + ret.ret.id;
                } else {
                    if (ret.code == '302') {
                        $("#already_apply_div").removeClass('aui-hide');
                    } else {
                        dialog_show({msg: ret.message, buttons: ['确定']}, null);
                    }
                }
                toast_hide();
            })
        }

        //关闭报名成功提醒
        function closeSuccessTip() {
            $("#apply_success_div").addClass('aui-hide');
        }

        //关闭已经报名提醒
        function closeAlreadyApplyTip() {
            $("#already_apply_div").addClass('aui-hide');
        }

        //删除图片
        function clickDelImg(index) {
            dialog_show({msg: '确定删除图片？'}, function (ret) {
                console.log("ret:" + JSON.stringify(ret));
                if (ret.buttonIndex == 2) {
                    img_arr.splice(index, 1);
                    loadWorksHtml();
                }
            })
        }

    </script>
@endsection