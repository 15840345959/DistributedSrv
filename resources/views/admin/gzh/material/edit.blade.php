@extends('admin.layouts.app')

@section('content')

    <div class="page-container">
        <form class="form form-horizontal" id="form-edit">
            {{csrf_field()}}
            <div class="row cl hidden">
                <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>id：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input id="id" name="id" type="text" class="input-text"
                           value="{{ isset($data->id) ? $data->id : '' }}" placeholder="公众号素材id">
                </div>
            </div>
            <div class="row cl hidden">
                <label class="form-label col-xs-4 col-sm-2">busi_name：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input id="busi_name" name="busi_name" type="text" class="input-text" readonly
                           value="{{ isset($data->busi_name) ? $data->busi_name : '' }}" placeholder="busi_name">
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2">公众号：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input id="busi_name_str" name="busi_name_str" type="text" class="input-text" readonly disabled
                           value="{{ isset($data->busi_name_str) ? $data->busi_name_str : '' }}" placeholder="公众号">
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>素材类型：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <span class="select-box" style="width:250px">
                    <select class="select" name="type" id="type" size="1" onchange="selectType();">
                        @foreach(\App\Components\Utils::MATERIAL_TYPE_VAL as $key=>$value)
                            <option value="{{$key}}">{{$value}}</option>
                        @endforeach
                    </select>
                    </span>
                </div>
            </div>
            <div class="row cl video-title hidden">
                <label class="form-label col-xs-4 col-sm-2">标题：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input id="title" name="title" type="text" class="input-text"
                           value="{{ isset($data->title) ? $data->title : '' }}" placeholder="请输入视频标题">
                </div>
            </div>
            <div class="row cl video-description hidden">
                <label class="form-label col-xs-4 col-sm-2">描述：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <textarea class="textarea" placeholder="请输入素材描述..." rows="" cols="" name="description"
                              id="description">{{ isset($data->description) ? $data->description : '' }}</textarea>
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>选择素材：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <span class="btn-upload form-group">
                        <input class="input-text upload-url radius" type="text" name="material_local_path"
                               id="material_local_path"
                               readonly="">
                        <a href="javascript:void();" class="btn btn-primary radius upload-btn"><i
                                    class="Hui-iconfont"></i> 浏览文件</a>
                        <input type="file" multiple="" id="material" name="material" class="input-file"
                               onchange="selectFile();">
				    </span>
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2">素材备注：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input id="remark" name="remark" type="text" class="input-text"
                           value="{{ isset($data->remark) ? $data->remark : '' }}" placeholder="请输入素材备注">
                </div>
            </div>
            <div class="row cl c-primary">
                <label class="form-label col-xs-4 col-sm-2">特殊说明</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <div>
                        <span class="c-danger">图片素材请上传jpg文件格式</span>
                    </div>
                    <div>
                        <span class="c-999">请根据素材类型上传相应的素材资源</span>
                    </div>
                    <div>
                        <span class="c-999">图片（image）: 2M，支持bmp/png/jpeg/jpg/gif格式</span>
                    </div>
                    <div>
                        <span class="c-999">语音（voice）：2M，播放长度不超过60s，mp3/wma/wav/amr格式</span>
                    </div>
                    <div>
                        <span class="c-999">视频（video）：10MB，支持MP4格式</span>
                    </div>
                </div>
            </div>
            <div class="row cl mt-20">
                <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-2">
                    <button class="btn btn-primary radius" type="submit"><i class="Hui-iconfont">&#xe632;</i> 保存素材
                    </button>
                </div>
            </div>
        </form>
    </div>

@endsection

@section('script')
    <script type="text/javascript">

        $(function () {
            //获取七牛token
            initQNUploader();
            $("#form-edit").validate({
                rules: {
                    title: {
                        required: true,
                    },
                    material: {
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
                        url: "{{ URL::asset('/admin/gzh/material/edit')}}",
                        success: function (ret) {
                            console.log(JSON.stringify(ret));
                            if (ret.result) {
                                layer.msg('保存成功', {icon: 1, time: 1000});
                                setTimeout(function () {
                                    var index = parent.layer.getFrameIndex(window.name);
                                    parent.$("#search_form").submit();
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

        //选中文件
        function selectFile() {
            console.log("selectFile");
            var material = $("#material").val();
            console.log("selectFile material：" + material);
            $("#remark").val(getFileName(material));        //获取文件名称
        }

        //选中素材类型
        function selectType() {
            console.log("selectType");
            //选中视频特殊处理
            var type = $("#type").val();
            if (type == 'video') {
                $(".video-title").removeClass('hidden');
                $(".video-description").removeClass('hidden');
            } else {
                $(".video-title").addClass('hidden');
                $(".video-description").addClass('hidden');
            }
            //选中图文
            if (type == 'article') {
                var type = $("#type").val('image');
                layer.msg('暂时不支持图文消息', {icon: 2, time: 1000});
            }
        }

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
//                                            alert(alert(JSON.stringify(file)));
                        });
                    },
                    'BeforeUpload': function (up, file) {
                        // 每个文件上传前，处理相关的事情
//                        console.log("BeforeUpload up:" + up + " file:" + JSON.stringify(file));
                    },
                    'UploadProgress': function (up, file) {
                        // 每个文件上传时，处理相关的事情
//                        console.log("UploadProgress up:" + up + " file:" + JSON.stringify(file));
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
                        $("#img").val(sourceLink);
                        $("#pickfiles").attr('src', qiniuUrlTool(sourceLink, "head_icon"));
//                        console.log($("#pickfiles").attr('src'));
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