@extends('admin.layouts.app')

@section('content')

    <div class="page-container">
        <style>
            .mask {
                position: absolute;
                width: 100%;
                height: 100%;
                border: solid 1px #ddd;
                background: rgba(0, 0, 0, .25);
                z-index: 99;
            }
        </style>
        <div id="preview" class="mask hidden" style="text-align: center;" onclick="clickCloseMask()">
            <img src=""
                 style="width: 600px;height: 600px;position: absolute;top: 50%;left: 50%;margin-top: -300px;margin-left: -300px;">
            {{--<div class="maskBar text-c">遮罩条</div>--}}
        </div>

        <form class="form form-horizontal" id="form-edit">
            {{csrf_field()}}
            <div class="row cl hidden">
                <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>id：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input id="id" name="id" type="text" class="input-text"
                           value="{{ isset($data->id) ? $data->id : '' }}" placeholder="参赛选手id">
                </div>
            </div>
            <div class="row cl hidden">
                <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>大赛id：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input id="activity_id" name="activity_id" type="text" class="input-text"
                           value="{{ isset($data->activity_id) ? $data->activity_id : '' }}" placeholder="大赛id">
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>选手名称：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input id="name" name="name" type="text" class="input-text"
                           value="{{ isset($data->name) ? $data->name : '' }}" placeholder="选手名称">
                </div>
            </div>
            <div class="row cl item c-999">
                <label class="form-label col-xs-4 col-sm-2"></label>
                <div class="formControls col-xs-8 col-sm-9">
                    <div>
                        <span>如果从后台添加选手，可以先上传作品图片，名字将自动通过作品图片名补齐，即如果图片命名为刘阿伟.png，则上传图片后，name自动使用刘阿伟补齐</span>
                    </div>
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2">选手电话：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input id="phonenum" name="phonenum" type="text" class="input-text"
                           value="{{ isset($data->phonenum) ? $data->phonenum : '' }}" placeholder="选手电话">
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2">参赛宣言：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input id="declaration" name="declaration" type="text" class="input-text"
                           value="{{ isset($data->declaration) ? $data->declaration : '' }}" placeholder="参赛宣言">
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2">作品名称：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input id="work_name" name="work_name" type="text" class="input-text"
                           value="{{ isset($data->work_name) ? $data->work_name : '' }}" placeholder="作品名称">
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2">作品说明：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input id="work_desc" name="work_desc" type="text" class="input-text"
                           value="{{ isset($data->work_desc) ? $data->work_desc : '' }}" placeholder="作品说明">
                </div>
            </div>
            <div class="row cl item">
                <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>作品：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input id="img" name="img" type="text" class="input-text" style="width: 85%"
                           value="{{ isset($data->img) ? $data->img : ''}}"
                           placeholder="请输入图片地址">
                    <div style="width: 100%;margin-top: 10px;">
                        <style>
                            .preview, .upload li {
                                margin: 0;
                                padding: 0;
                                list-style-type: none
                            }

                            .preview {
                                border: 1px solid #d7d7d7;
                                padding: 15px 5px 5px 15px;
                                zoom: 1;
                                position: relative;
                                width: 100%;
                            }

                            .preview:after {
                                display: block;
                                clear: both;
                                content: "";
                                visibility: hidden;
                                height: 0
                            }

                            .preview .item {
                                width: 150px;
                                height: 120px;
                                float: left;
                                margin: 0 10px 10px 0;
                                background: #f8f8f8;
                                position: relative;
                                border-radius: 4px;
                                background-size: contain;
                                background-position: center center;
                                background-repeat: no-repeat;
                                border: 1px solid #d7d7d7;
                                overflow: hidden
                            }

                            .preview .item .filename {
                                font-size: 12px;
                                width: 90%;
                                left: 5%;
                                position: absolute;
                                top: 70%;
                                line-height: 1.3em;
                                height: 2.6em;
                                overflow: hidden;
                                text-align: center
                            }

                            .preview .item.error {
                                border-color: #f20
                            }

                            .preview .item.error::after {
                                content: "";
                                background: rgba(255, 255, 255, .8);
                                position: absolute;
                                width: 100%;
                                height: 100%;
                                z-index: 9;
                                display: block;
                                line-height: 100%;
                                text-align: center
                            }

                            .preview .item.error::before {
                                content: attr(data-error);
                                position: absolute;
                                padding: 10px;
                                z-index: 10;
                                display: block;
                                font-size: 12px;
                                color: #f20;
                                top: 0
                            }

                            .preview .item svg.icon {
                                position: absolute;
                                height: 40%;
                                top: 20%;
                                left: 0;
                                width: 100%
                            }

                            .preview .item svg.progress {
                                position: absolute;
                                bottom: 0;
                                width: 100%;
                                height: 50%
                            }

                            .preview .item .progressnum {
                                width: 40px;
                                height: 40px;
                                border-radius: 40px;
                                text-align: center;
                                line-height: 40px;
                                font-size: 12px;
                                color: #fff;
                                position: absolute;
                                left: 50%;
                                margin-left: -20px;
                                top: 50%;
                                margin-top: -20px;
                                background: rgba(17, 89, 164, 0.5)
                            }

                            .preview .item.add svg {
                                top: 30%
                            }

                            .preview .item.success::after {
                                position: absolute;
                                background: rgba(0, 0, 0, .6);
                                content: "";
                                left: 0;
                                right: 0;
                                top: 0;
                                bottom: 0;
                                opacity: 0;
                                transition: all .3s
                            }

                            .preview .item.success:hover::after {
                                opacity: 1
                            }

                            .preview .item.success svg.delete, .preview .item.success svg.look {
                                position: absolute;
                                height: 30px;
                                top: 50%;
                                margin-top: -15px;
                                left: 50%;
                                color: #fff;
                                z-index: 10;
                                transition: all .3s;
                                cursor: pointer
                            }

                            .preview .item.success svg.delete {
                                margin-left: -35px;
                                height: 26px;
                                margin-top: -14px;
                                left: -30px
                            }

                            .preview .item.success svg.look {
                                margin-left: 10px;
                                left: 105%
                            }

                            .preview .item.success:hover svg.delete {
                                margin-left: -35px;
                                left: 50%;
                                height: 26px;
                                margin-top: -14px;
                                transition: all .3s
                            }

                            .preview .item.success:hover svg.look {
                                margin-left: 10px;
                                left: 50%;
                                transition: all .3s
                            }

                            .preview .item.delete {
                                opacity: .2;
                                transition: all .3s
                            }

                            .preview input[type='file'] {
                                display: none
                            }

                            .preview.multiple.empty {
                                height: 160px;
                                width: 100%
                            }

                            .preview.multiple.empty .add {
                                width: 100%;
                                position: absolute;
                                top: 50%;
                                margin-top: -30px;
                                height: 36px;
                                background: 0;
                                border: 0;
                                left: 0;
                                overflow: inherit
                            }

                            .preview.multiple.empty .add::after {
                                content: "ç‚¹å‡»ä¸Šä¼ æ–‡ä»¶";
                                width: 180px;
                                position: absolute;
                                height: 36px;
                                background: #0e90d2;
                                left: 50%;
                                margin-left: -90px;
                                display: block;
                                z-index: 9;
                                visibility: visible;
                                text-align: center;
                                color: #fff;
                                line-height: 36px;
                                font-size: 14px;
                                border: 0 none;
                                border-radius: 0
                            }

                            .preview.multiple.empty .add:hover::after {
                                background-color: #0a70c2
                            }

                            .preview.multiple.empty .add svg {
                                display: none
                            }

                            .preview.multiple.empty .add::before {
                                content: "æˆ–è€…å°†æ–‡ä»¶æ‹–åˆ°æ­¤å¤„,æœ€å¤šå¯ä»¥ä¸Šä¼  " attr(data-num) " ä¸ª" attr(data-type) "æ ¼å¼æ–‡ä»¶";
                                width: 100%;
                                text-align: center;
                                position: absolute;
                                bottom: -30px;
                                font-size: 12px;
                                margin-top: 14px;
                                left: 0;
                                color: #999;
                                white-space: nowrap
                            }

                            .preview.one {
                                width: 150px;
                                height: 150px;
                                padding: 0
                            }

                            .preview.one li {
                                height: 100%;
                                width: 100%;
                                margin: 0;
                                padding: 0
                            }

                            .preview.one li.add svg {
                                opacity: 0;
                                transition: all .3s;
                                margin-top: -30px
                            }

                            .preview.one li.add:hover svg {
                                opacity: 1;
                                transition: all .3s
                            }

                            .preview.one.empty li.add svg {
                                opacity: 1;
                                transition: all .3s
                            }

                            .preview.one .item {
                                border: 0 none;
                                border-radius: 0
                            }

                            .preview.one .add {
                                position: absolute;
                                top: 30px;
                                right: 0;
                                left: 0;
                                background: 0
                            }

                            .preview.one .item.success svg.delete, .preview.one .item.success svg.look {
                                top: 0;
                                margin-top: 5px
                            }

                            .preview.one .item.success svg.look {
                                margin-top: 3px
                            }

                            .preview.dragenter {
                                border: 2px dashed #d7d7d7;
                                box-shadow: 0 1px 5px rgba(0, 0, 0, .7);
                                display: table
                            }

                            .preview.dragenter::after {
                                content: "";
                                background: rgba(255, 255, 255, .9);
                                z-index: 998;
                                position: absolute;
                                left: 0;
                                top: 0;
                                height: 100%;
                                width: 100%;
                                vertical-align: middle;
                                display: table-cell;
                                visibility: visible
                            }

                            .preview.dragenter::before {
                                content: "å°†æ–‡ä»¶æ‹–åˆ°è¿™é‡Œä¸Šä¼ ";
                                position: absolute;
                                width: 100%;
                                top: 50%;
                                z-index: 999;
                                width: 100%;
                                text-align: center;
                                margin-top: -12px;
                                color: #999
                            }
                        </style>

                        <div class="preview multiple">

                            <div id="upload-content">

                                @if(isset($data->img))
                                    @foreach(explode(",", $data->img) as $item)
                                        <li id="{{explode('.', explode("/", $item)[count(explode("/", $item)) - 1])[0]}}"
                                            class="item success"
                                            data-filename="{{explode('.', explode("/", $item)[count(explode("/", $item)) - 1])[0]}}"
                                            data-error="" style="background-image: url({{$item}});">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="delete" version="1"
                                                 viewBox="0 0 1024 1024"
                                                 onclick="clickDelete('{{explode('.', explode("/", $item)[count(explode("/", $item)) - 1])[0]}}')">
                                                <path fill="#fff"
                                                      d="M512 70a439 439 0 0 1 442 442 439 439 0 0 1-442 442A439 439 0 0 1 70 512 439 439 0 0 1 512 70m0-40a482 482 0 1 0 0 964 482 482 0 0 0 0-964zm114 253v-1c0-21-17-38-38-38H436c-21 0-38 17-38 38v1H282v74h460v-74H626zM321 396v346c0 21 17 38 38 38h306c21 0 38-17 38-38V396H321zm114 306h-76V474h76v228zm115 0h-76V474h76v228zm115 0h-76V474h76v228z"></path>
                                            </svg>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="look" version="1"
                                                 viewBox="0 0 1024 1024" onclick="preview('{{$item}}')">
                                                <path fill="#fff"
                                                      d="M451 835a386 386 0 1 1 0-771 386 386 0 0 1 0 771zm0-675a291 291 0 1 0 0 581 291 291 0 0 0 0-581zm450 798c-15 0-30-5-42-17L658 740a58 58 0 0 1 83-82l201 201a58 58 0 0 1-41 99"></path>
                                            </svg>
                                        </li>
                                    @endforeach
                                @endif

                            </div>

                            <li id="pickfiles" class="item add" data-num="10" data-type="png,jpg,jpeg,gif" style="margin-left: 0px;">
                                <svg class="icon" viewBox="0 0 1024 1024" version="1" xmlns="http://www.w3.org/2000/svg" width="200" height="200">
                                    <defs>
                                        <style></style>
                                    </defs>
                                    <path d="M737 436a174 174 0 0 1 172 172 172 172 0 0 1-172 172c-69 1-69 107 0 106 152-2 276-125 278-278S886 332 737 330c-69-1-69 105 0 106zM285 779a174 174 0 0 1-172-172 172 172 0 0 1 172-172c68-1 69-106 0-106A282 282 0 0 0 7 607a281 281 0 0 0 278 278c69 1 68-105 0-106z"
                                          fill="#4A5699"></path>
                                    <path d="M340 384a174 174 0 0 1 172-172 172 172 0 0 1 172 172c1 68 106 68 106 0a282 282 0 0 0-278-278 281 281 0 0 0-278 278c-1 68 105 68 106 0z"
                                          fill="#C45FA0"></path>
                                    <path d="M545 473c17 17 17 43 0 60L422 656a42 42 0 0 1-60-60l123-123c17-16 43-16 60 0z"
                                          fill="#F39A2B"></path>
                                    <path d="M485 473c17-16 44-16 60 0l123 123a42 42 0 0 1-60 60L485 533a42 42 0 0 1 0-60z"
                                          fill="#F39A2B"></path>
                                    <path d="M514 634c24 0 43 20 43 43v179a43 43 0 0 1-86 0V677c0-23 19-43 43-43z"
                                          fill="#E5594F"></path>
                                </svg>
                            </li>
                            <input type="file" name="file" multiple="multiple">
                            <input type="hidden" name="upload" value="1.png">
                        </div>
                    </div>


                    <div style="font-size: 12px;margin-top: 10px;" class="text-gray">*请上传600*600比例尺寸图片</div>
                </div>
            </div>

            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2">视频：</label>
                <div class="formControls col-xs-8 col-sm-9">
                <span id="video_container" class="btn-upload form-group">
                    <input class="input-text upload-url radius" type="text" name="video"
                           id="video" value="{{$data->video}}">
                    <a id="video_pickfiles" href="javascript:void();" class="btn btn-primary radius upload-btn"><i
                                class="Hui-iconfont"></i> 浏览文件</a>
                </span>
                </div>
            </div>
            <div class="row cl item c-999">
                <label class="form-label col-xs-4 col-sm-2"></label>
                <div class="formControls col-xs-8 col-sm-9">
                    <div>
                        <video id="video_video" src="{{$data->video}}" controls="controls"
                               style="width: 400px;height: 250px;">
                        </video>
                    </div>
                </div>
            </div>
            <div class="row cl item c-999">
                <label class="form-label col-xs-4 col-sm-2"></label>
                <div class="formControls col-xs-8 col-sm-9">
                    <div>
                        <span>如果不传视频则前端不显示，视频请上传mp4格式文件</span>
                    </div>
                </div>
            </div>

            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2">票数：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input id="vote_num" name="vote_num" type="number" class="input-text"
                           value="{{ isset($data->vote_num) ? $data->vote_num : '0' }}" placeholder="票数">
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2">礼物总金额：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input id="gift_money" name="gift_money" type="number" class="input-text" disabled
                           value="{{ isset($data->gift_money) ? $data->gift_money : '0' }}" placeholder="礼物总金额">
                    {{--<span class="ml-5 c-danger">该字段不能修改，确保资金准确</span>--}}
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2">展示数：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input id="show_num" name="show_num" type="number" class="input-text"
                           value="{{ isset($data->show_num) ? $data->show_num : '0' }}" placeholder="展示数">
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2">分享数：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input id="share_num" name="share_num" type="number" class="input-text"
                           value="{{ isset($data->share_num) ? $data->share_num : '0' }}" placeholder="展示数">
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2">粉丝数：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input id="fans_num" name="fans_num" type="number" class="input-text"
                           value="{{ isset($data->fans_num) ? $data->fans_num : '0' }}" placeholder="展示数">
                </div>
            </div>
            <div class="row cl mt-20">
                <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-2">
                    <button class="btn btn-primary radius" type="submit"><i class="Hui-iconfont">&#xe632;</i> 保存选手
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script id="init-upload-content-template" type="text/x-dot-template">
        <li id="@{{= it.id}}" class="item" data-filename="@{{= it.id}}" data-error="" style="background-image: url();">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="300" height="300"
                 class="progress" style="height: 170%;">
                <g fill="rgba(17,89,164,0.1)" transform="translate(-56.6667 0)">
                    <path d="M 0 70 Q 75 39, 150 70 T 300 70 T 450 70 T 600 70 T 750 70 V 320 H 0 V 0"></path>
                    <animateTransform attributeName="transform" attributeType="XML" type="translate" from="0" to="-300"
                                      dur="1.5s" repeatCount="indefinite"></animateTransform>
                </g>
                <g fill="rgba(17,89,164,0.15)" transform="translate(-208.056 0)">
                    <path d="M 0 70 Q 87.5 47, 175 70 T 350 70 T 525 70 T 700 70 T 875 70 T 1050 70 V 320 H 0 V 0">
                    </path>
                    <animateTransform attributeName="transform" attributeType="XML" type="translate" from="0" to="-350"
                                      dur="3s" repeatCount="indefinite"></animateTransform>
                </g>
            </svg>
            <div class="progressnum">0</div>
        </li>
    </script>


    <script id="upload-success-content-template" type="text/x-dot-template">
        <svg xmlns="http://www.w3.org/2000/svg" class="delete" version="1" viewBox="0 0 1024 1024"
             onclick="clickDelete('@{{=it.id}}')">
            <path fill="#fff"
                  d="M512 70a439 439 0 0 1 442 442 439 439 0 0 1-442 442A439 439 0 0 1 70 512 439 439 0 0 1 512 70m0-40a482 482 0 1 0 0 964 482 482 0 0 0 0-964zm114 253v-1c0-21-17-38-38-38H436c-21 0-38 17-38 38v1H282v74h460v-74H626zM321 396v346c0 21 17 38 38 38h306c21 0 38-17 38-38V396H321zm114 306h-76V474h76v228zm115 0h-76V474h76v228zm115 0h-76V474h76v228z"></path>
        </svg>
        <svg xmlns="http://www.w3.org/2000/svg" class="look" version="1" viewBox="0 0 1024 1024"
             onclick="preview('@{{=it.sourceLink}}')">
            <path fill="#fff"
                  d="M451 835a386 386 0 1 1 0-771 386 386 0 0 1 0 771zm0-675a291 291 0 1 0 0 581 291 291 0 0 0 0-581zm450 798c-15 0-30-5-42-17L658 740a58 58 0 0 1 83-82l201 201a58 58 0 0 1-41 99"></path>
        </svg>
    </script>

@endsection

@section('script')
    <script type="text/javascript" src="{{asset('js/doT.min.js')}}"></script>
    <script type="text/javascript">
        var img_count = {{count(explode(',', $data->img))}}

        $(function () {
            //获取七牛token
            initQNUploader();
            initVideoQNUploader();      //上传视频

            //表单提交
            $("#form-edit").validate({
                rules: {
                    name: {
                        required: true,
                    },
                },
                onkeyup: false,
                focusCleanup: true,
                success: "valid",
                submitHandler: function (form) {

                    var index = layer.load(2, {time: 10 * 1000}); //加载

                    $(form).ajaxSubmit({
                        type: 'POST',
                        url: "{{ URL::asset('/admin/vote/voteUser/edit')}}",
                        success: function (ret) {
                            console.log(JSON.stringify(ret));
                            if (ret.result) {
                                layer.msg('保存成功', {icon: 1, time: 1000});
                                setTimeout(function () {
                                    var index = parent.layer.getFrameIndex(window.name);
                                    parent.$('.btn-refresh').click();
                                    parent.layer.close(index);
                                }, 500)
                            } else {
                                layer.msg(ret.message, {icon: 2, time: 1000});
                            }

                            layer.close(index);
                        },
                        error: function (XmlHttpRequest, textStatus, errorThrown) {
                            layer.msg('保存失败', {icon: 2, time: 1000});
                            console.log("XmlHttpRequest:" + JSON.stringify(XmlHttpRequest));
                            console.log("textStatus:" + textStatus);
                            console.log("errorThrown:" + errorThrown);
                        }
                    });
                }

            });
        });


        //初始化七牛上传模块
        function initQNUploader() {
            var uploader = Qiniu.uploader({
                runtimes: 'html5,flash,html4',      // 上传模式，依次退化
                browse_button: 'pickfiles',         // 上传选择的点选按钮，必需
                container: 'upload-content',//上传按钮的上级元素ID
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
                        console.log('FilesAdded up is : ' + JSON.stringify(up))
                        // console.log('FilesAdded file is : ' + JSON.stringify(files))

                        if (img_count >= 5) {
                            alert('图片总数大于5张！请先删除')
                            return
                        }

                        var real_up_count = 0
                        var total_up_count = img_count + files.length

                        if (judgeIsAnyNullStr($("#name").val())) {
                            $("#name").val(files[0].name.split(".")[0])
                        }

                        if (total_up_count > 5) {
                            // real_up_count = up_count - 5
                            // alert('已有' + img_count + '张图片，还可以上传' + real_up_count + '张')
                            // return

                            real_up_count = 5 - img_count
                            up.files.splice(real_up_count)
                            files.splice(real_up_count)

                            console.log('FilesAdded up is : ' + JSON.stringify(up))

                            layer.alert('图片总数大于5张！系统将自动截取至5张图片');
                        }

                        plupload.each(files, function (file, index) {
                            // 文件添加进队列后，处理相关的事情

                            // console.log('file is : ' + JSON.stringify(file))

                            loadInitUploadHtml(file)
                            img_count++
                        })
                    },
                    'BeforeUpload': function (up, file) {
                        // 每个文件上传前，处理相关的事情
                        // console.log("BeforeUpload up:" + up + " file:" + JSON.stringify(up))
                        // console.log("BeforeUpload up:" + file + " file:" + JSON.stringify(file))


                    },
                    'UploadProgress': function (up, file) {
                        // 每个文件上传时，处理相关的事情
                        // console.log("UploadProgress up:" + JSON.stringify(up))
                        // console.log("UploadProgress file:" + JSON.stringify(file))

                        $('#' + file.id).find('.progressnum').first().text(file.percent)
                    },
                    'FileUploaded': function (up, file, info) {
                        // 每个文件上传成功后，处理相关的事情
                        // console.log("FileUploaded up:" + JSON.stringify(up))
                        // console.log("FileUploaded file:" + JSON.stringify(file))
                        // console.log("FileUploaded info:" + JSON.stringify(info))


                        // 其中info是文件上传成功后，服务端返回的json，形式如：
                        // {
                        //    "hash": "Fh8xVqod2MQ1mocfI4S4KpRL6D98",
                        //    "key": "gogopher.jpg"
                        //  }


                        var domain = up.getOption('domain');
                        var res = JSON.parse(info);
                        //获取上传成功后的文件的Url
                        var sourceLink = domain + res.key;

                        // $("#img").val(sourceLink)
                        // $("#pickfiles").attr('src', sourceLink)

                        $('#' + file.id).addClass('success').css("background-image", "url(" + sourceLink + ")").empty()

                        file.sourceLink = sourceLink

                        loadSuccessUploadHtml(file.id, file)

                        var img_val = $('#img').val()
                        // console.log('img_val is : ' + img_val)

                        if (img_val) {
                            var img_arr = img_val.split(',')
                        } else {
                            var img_arr = []
                        }

                        // console.log('img_arr is : ' + JSON.stringify(img_arr))

                        img_arr.push(sourceLink)

                        console.log('img_arr is : ' + JSON.stringify(img_arr))
                        console.log('img_arr length is : ' + JSON.stringify(img_arr.length))

                        var img_arr_str = img_arr.join(',')

                        $('#img').val(img_arr_str)
                    },
                    'Error': function (up, err, errTip) {
                        //上传出错时，处理相关的事情
                        console.log("Error up:" + JSON.stringify(up))
                        console.log("Error err:" + JSON.stringify(err))
                        console.log("Error errTip:" + JSON.stringify(errTip))

                        // $('#' + file.id).addClass('error')
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

        function loadInitUploadHtml(data) {
            // consoledebug.log('loadInitUploadHtml data is : ' + JSON.stringify((data)))

            var interText = doT.template($("#init-upload-content-template").text())
            $("#upload-content").append(interText(data))
        }

        function loadSuccessUploadHtml(id, data) {
            // consoledebug.log('loadSuccessUploadHtml data is : ' + JSON.stringify((data)))

            var interText = doT.template($("#upload-success-content-template").text())
            $("#" + id).append(interText(data))
        }

        function clickDelete(id) {
            // console.log('clickDelete id is : ' + id)

            var img_val = $('#img').val()
            // console.log('img_val is : ' + img_val)

            var img_arr = img_val.split(',')
            // console.log('img_arr is : ' + JSON.stringify(img_arr))

            var index = findInArray(id, img_arr)

            if (index) {
                $('#' + id).remove()

                img_arr.splice(index, 1)

                // console.log('img_arr is : ' + JSON.stringify(img_arr))

                var img_arr_str = img_arr.join(',')

                $('#img').val(img_arr_str)

                img_count--
            }
        }

        function findInArray(el, arr) {
            for (var i = 0; i < arr.length; i++) {
                if (arr[i] === el) {
                    return i
                } else {
                    return -1
                }
            }
        }

        function preview(url) {
            console.log('preview url is : ' + url)

            $('#preview').find('img').first().attr('src', url)
            $('#preview').removeClass('hidden')
            $('#preview').addClass('hui-bouncein')

            $('#preview').on({
                'animationend': function () {
                    $('#text').removeClass()
                },
                'webkitAnimationEnd': function () {
                    $('#text').removeClass()
                }
            })
        }

        function clickCloseMask() {
            $('#preview').addClass('hidden')
        }

        //上传视频
        //初始化七牛上传模块
        function initVideoQNUploader() {
            var uploader = Qiniu.uploader({
                runtimes: 'html5,flash,html4',      // 上传模式，依次退化
                browse_button: 'video_pickfiles',         // 上传选择的点选按钮，必需
                container: 'video_container',//上传按钮的上级元素ID
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
//                                            alert(alert(JSON.stringify(file)));
                        });
                        if (judgeIsAnyNullStr($("#name").val())) {
                            $("#name").val(files[0].name.split(".")[0])
                        }
                        layer.load(2, {time: 10 * 1000});
                    },
                    'BeforeUpload': function (up, file) {
                        // 每个文件上传前，处理相关的事情
//                        console.log("BeforeUpload up:" + up + " file:" + JSON.stringify(file));
                    },
                    'UploadProgress': function (up, file) {
                        // 每个文件上传时，处理相关的事情
                        // console.log("UploadProgress up:" + JSON.stringify(up) + " file:" + JSON.stringify(file));
                        layer.tips(file.percent, '#video');
                    },
                    'FileUploaded': function (up, file, info) {
                        // 每个文件上传成功后，处理相关的事情
                        // 其中info是文件上传成功后，服务端返回的json，形式如：
                        // {
                        //    "hash": "Fh8xVqod2MQ1mocfI4S4KpRL6D98",
                        //    "key": "gogopher.jpg"
                        //  }
                        console.log(JSON.stringify(info));
                        var domain = up.getOption('domain');
                        var res = JSON.parse(info);
                        //获取上传成功后的文件的Url
                        var sourceLink = domain + res.key;
                        $("#video").val(sourceLink);
                        //音频播放
                        $("#video_video").attr("src", sourceLink);//更新url

                        layer.closeAll('loading');
                    },
                    'Error': function (up, err, errTip) {
                        //上传出错时，处理相关的事情
                        console.log(err + errTip);
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


    </script>
@endsection