<form class="form form-horizontal" id="form-edit">
    {{csrf_field()}}
    <div class="row cl hidden">
        <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>id：</label>
        <div class="formControls col-xs-8 col-sm-9">
            <input id="id" name="id" type="text" class="input-text"
                   value="{{ isset($data->id) ? $data->id : '' }}" placeholder="大赛id">
        </div>
    </div>
    <div class="row cl">
        <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>活动名称：</label>
        <div class="formControls col-xs-8 col-sm-9">
            <input id="name" name="name" type="text" class="input-text"
                   value="{{ isset($data->name) ? $data->name : '' }}" placeholder="请输入活动名称">
        </div>
    </div>
    <div class="row cl item c-999">
        <label class="form-label col-xs-4 col-sm-2"></label>
        <div class="formControls col-xs-8 col-sm-9">
            <div>
                <span>最多可以设置15个汉字</span>
            </div>
        </div>
    </div>
    <div class="row cl">
        <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>关键词设置：</label>
        <div class="formControls col-xs-8 col-sm-9">
            <input id="code" name="code" type="text" class="input-text"
                   value="{{ isset($data->code) ? $data->code : '' }}" placeholder="请输入关键词">
        </div>
    </div>
    <div class="row cl item c-999">
        <label class="form-label col-xs-4 col-sm-2"></label>
        <div class="formControls col-xs-8 col-sm-9">
            <div>
                <span>每个场3位字母，规则26个首字母代表省份（A代表直辖市、B代表自治区、C代表行政区、其他代表23个省）</span>
                <span class="c-primary">注意现阶段技术上未控制关键词规则，目前可以重复，如果有需要规避重复问题，请联系技术组处理</span>
            </div>
        </div>
    </div>
    <div class="row cl item">
        <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>活动缩略图：</label>
        <div class="formControls col-xs-8 col-sm-9">
            <input id="img" name="img" type="text" class="input-text" style=""
                   value="{{ isset($data->img) ? $data->img : ''}}"
                   placeholder="请输入选择图片">
            <div id="container" class="margin-top-10">
                <img id="pickfiles"
                     src="{{ isset($data->img) ? $data->img : URL::asset('/img/upload.png') }}"
                     style="width: 350px;">
            </div>
            <div style="font-size: 12px;margin-top: 10px;" class="c-999">*请上传800*600比例尺寸图片</div>
        </div>
    </div>
    <div class="row cl item">
        <label class="form-label col-xs-4 col-sm-2">活动简述：</label>
        <div class="formControls col-xs-8 col-sm-9">
                    <textarea id="intro" name="intro" class="textarea" placeholder="请输入活动简述..." rows=""
                              cols="">{{ isset($data->intro) ? $data->intro : '' }}</textarea>
        </div>
    </div>
    <div class="row cl item">
        <label class="form-label col-xs-4 col-sm-2">报名时间：</label>
        <div class="formControls col-xs-8 col-sm-9">
            <input id="apply_start_time" name="apply_start_time" type="datetime-local" class="input-text"
                   style="width:250px"
                   value="{{ isset($data->apply_start_time) ? str_replace(' ','T',$data->apply_start_time) : '' }}">-
            <input id="apply_end_time" name="apply_end_time" type="datetime-local" class="input-text"
                   style="width:250px"
                   value="{{ isset($data->apply_end_time) ? str_replace(' ','T',$data->apply_end_time) : '' }}">
        </div>
    </div>
    <div class="row cl item c-999">
        <label class="form-label col-xs-4 col-sm-2"></label>
        <div class="formControls col-xs-8 col-sm-9">
            <div>
                <span>报名时间默认两周；投票时间结束为15天，结束时间点为晚上10点10分不可更改。</span>
            </div>
        </div>
    </div>
    <div class="row cl item">
        <label class="form-label col-xs-4 col-sm-2">投票时间：</label>
        <div class="formControls col-xs-8 col-sm-9">
            <input id="vote_start_time" name="vote_start_time" type="datetime-local" class="input-text"
                   style="width:250px"
                   value="{{ isset($data->vote_start_time) ? str_replace(' ','T',$data->vote_start_time) : '' }}">-
            <input id="vote_end_time" name="vote_end_time" type="datetime-local" class="input-text"
                   style="width:250px"
                   value="{{ isset($data->vote_end_time) ? str_replace(' ','T',$data->vote_end_time) : '' }}">
        </div>
    </div>
    <div class="row cl item c-999">
        <label class="form-label col-xs-4 col-sm-2"></label>
        <div class="formControls col-xs-8 col-sm-9">
            <div>
                <span>选择投票的起止时间，投票时间为启动后一个周；当比赛人数达到30人后自动开启投票，在此之前显示报名人数不足。</span>
            </div>
        </div>
    </div>
    <div class="row cl item">
        <label class="form-label col-xs-4 col-sm-2">投票模式：</label>
        <div class="formControls col-xs-8 col-sm-9">
            @foreach(\App\Components\Utils::VOTE_MODE_VAL as $key=>$value)
                <div class="radio-box">
                    <input type="radio" id="vote_mode_{{$key}}" name="vote_mode"
                           value="{{$key}}" {{$data->vote_mode==strval($key)?'checked':''}}>
                    <label for="vote_mode_{{$key}}">{{$value}}</label>
                </div>
            @endforeach
        </div>
    </div>
    <div class="row cl mt-20">
        <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-2">
            <button class="btn btn-primary radius" type="submit"><i class="Hui-iconfont">&#xe632;</i> 保存配置
            </button>
        </div>
    </div>
</form>


<script type="text/javascript">

    $(function () {
        //获取七牛token
        initQNUploader();
        //表单提交
        $("#form-edit").validate({
            rules: {
                name: {
                    required: true,
                },
                img: {
                    required: true,
                },
                desc: {
                    required: true,
                },
                activity_start_time: {
                    required: true,
                },
                activity_end_time: {
                    required: true,
                },
                apply_start_time: {
                    required: true,
                },
                apply_end_time: {
                    required: true,
                },
                vote_start_time: {
                    required: true,
                },
                vote_end_time: {
                    required: true,
                },
                vote_mode: {
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
                    url: "{{ URL::asset('/admin/vote/voteActivity/edit')}}",
                    success: function (ret) {
                        console.log(JSON.stringify(ret));
                        if (ret.result) {
                            layer.msg('保存成功', {icon: 1, time: 1000});
                            location.replace('{{URL::asset('/admin/vote/voteActivity/edit')}}?item={{$item}}&id=' + ret.ret.id);
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
                    $("#pickfiles").attr('src', sourceLink);
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
