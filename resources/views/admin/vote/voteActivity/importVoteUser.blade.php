@extends('admin.layouts.app')

@section('content')

    <div class="page-container">
        <form class="form form-horizontal" id="form-edit">
            {{csrf_field()}}
            <div class="row cl hidden">
                <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>大赛id：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input id="activity_id" name="activity_id" type="text" class="input-text"
                           value="{{ isset($data->id) ? $data->id : '' }}" placeholder="大赛id">
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>大赛名称：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input id="name" name="name" type="text" class="input-text"
                           value="{{ isset($data->name) ? $data->name : '' }}" placeholder="大赛名称" disabled>
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2">导入选手：</label>
                <div class="formControls col-xs-8 col-sm-9">
                <span id="container" class="btn-upload form-group">
                    <input class="input-text upload-url radius" type="text" name=""
                           id="" value="" placeholder="请选择选手图片，图片名设置为选手名">
                    <a id="pickfiles" href="javascript:void();" class="btn btn-primary radius upload-btn"><i
                                class="Hui-iconfont"></i> 选择选手</a>
                </span>
                </div>
            </div>
            <div class="row cl item c-999">
                <label class="form-label col-xs-4 col-sm-2"></label>
                <div class="formControls col-xs-8 col-sm-9">
                    <div>
                        <span>支持多选功能，一般选手照片名为选手名称，系统将自动摘取选手名称</span>
                    </div>
                </div>
            </div>
            <div id="message-content" class="mt-20">

            </div>

            <div class="row cl mt-20">
                <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-2">
                    <button class="btn btn-primary radius" type="submit"><i class="Hui-iconfont">&#xe632;</i> 批量导入选手
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script id="message-content-template" type="text/x-dot-template">
        <div id="@{{=it.id}}" class="row cl">
            <label class="form-label col-xs-4 col-sm-2"></label>
            <div class="formControls col-xs-8 col-sm-9">
                <input name="name[]" type="text" class="input-text"
                       value="@{{=it.name}}" placeholder="选手姓名" style="width: 250px;">
                <input name="img[]" id="img_@{{=it.id}}" type="text" class="input-text"
                       value="@{{=it.img}}" placeholder="选手作品" style="width: 450px;">
                <span class="ml-10 c-primary" onclick="clickDelVoteUser('@{{=it.id}}')">删除</span>
            </div>
        </div>
    </script>

@endsection

@section('script')

    <script type="text/javascript" src="{{ URL::asset('/js/doT.min.js') }}"></script>

    <script type="text/javascript">

        $(function () {
            //获取七牛token
            initQNUploader();
            //表单提交
            $("#form-edit").validate({
                rules: {},
                onkeyup: false,
                focusCleanup: true,
                success: "valid",
                submitHandler: function (form) {

                    var index = layer.load(2, {time: 10 * 1000}); //加载

                    $(form).ajaxSubmit({
                        type: 'POST',
                        url: "{{ URL::asset('/admin/vote/voteActivity/importVoteUser')}}",
                        success: function (ret) {
                            console.log(JSON.stringify(ret));
                            if (ret.result) {
                                layer.msg('导入成功', {icon: 1, time: 1000});
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

        //批量导入投票用户数组
        var vote_user_arr = [];
        var vote_user_upload_count = 0;     //批量导入用户计数


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
                        total_vote_user_count = 0;
                        plupload.each(files, function (file) {
                            // 文件添加进队列后，处理相关的事情
//                                            alert(alert(JSON.stringify(file)));
                            console.log(JSON.stringify(file));
                            var vote_user_obj = {
                                id: file.id,
                                name: file.name.split(".")[0]
                            }
                            vote_user_arr.push(vote_user_obj);  //推入数组
                        });

                        //完成后加载item对象
                        for (var i = 0; i < vote_user_arr.length; i++) {
                            var vote_user_obj = vote_user_arr[i];
                            vote_user_obj.img = "";
                            //如果dom元素存在，说明已经加入了
                            if (!is_dom_exist(vote_user_obj.id)) {
                                var interText = doT.template($("#message-content-template").text());
                                $("#message-content").append(interText(vote_user_obj));
                            }
                        }
                        var index = layer.load(2, {time: 10 * 1000}); //加载
                    },
                    'BeforeUpload': function (up, file) {
                        // 每个文件上传前，处理相关的事情
//                        console.log("BeforeUpload up:" + up + " file:" + JSON.stringify(file));
                    },
                    'UploadProgress': function (up, file) {
                        // 每个文件上传时，处理相关的事情
                        console.log("UploadProgress up:" + up + " file:" + JSON.stringify(file));
                        layer.tips(file.percent, '#img_' + file.id);
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
                        var key = res.key.split(".")[0];       //注意此处key是有.文件类型的，所以要把文件类型去掉
                        var vote_user_obj = getObjInArrById(key, vote_user_arr);
                        vote_user_obj.img = domain + res.key;
                        $("#img_" + key).val(domain + res.key);

                        vote_user_upload_count++;       //计数器++
                        layer.msg('导入中...' + vote_user_upload_count + '/' + vote_user_arr.length, {icon: 16});
                        if (vote_user_upload_count == vote_user_arr) {
                            layer.close();
                        }
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

        //删除用户
        function clickDelVoteUser(dom_id) {
            $("#" + dom_id).remove();
        }

    </script>
@endsection