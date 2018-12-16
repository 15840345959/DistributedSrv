<form class="form form-horizontal" id="form-edit">
    {{csrf_field()}}
    <div class="row cl hidden">
        <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>id：</label>
        <div class="formControls col-xs-8 col-sm-9">
            <input id="id" name="id" type="text" class="input-text"
                   value="{{ isset($data->id) ? $data->id : '' }}" placeholder="大赛id">
        </div>
    </div>
    <div class="row cl item hide">
        <label class="form-label col-xs-4 col-sm-2">人机验码：</label>
        <div class="formControls col-xs-8 col-sm-9">
            @foreach(\App\Components\Utils::VOTE_VERTIFY_MODE_VAL as $key=>$value)
                <div class="radio-box">
                    <input type="radio" id="vote_vertify_mode_{{$key}}" name="vote_vertify_mode"
                           value="{{$key}}" {{strval($key) == 0 ? 'checked' : ''}}>
                    <label for="vote_vertify_mode_{{$key}}">{{$value}}</label>
                </div>
            @endforeach
        </div>
    </div>
    <div class="row cl item c-999 hide">
        <label class="form-label col-xs-4 col-sm-2"></label>
        <div class="formControls col-xs-8 col-sm-9">
            <div>
                <span>当天票数超过50之后自动开启滑动人机验证</span>
            </div>
        </div>
    </div>
    <div class="row cl item hide">
        <label class="form-label col-xs-4 col-sm-2">投票审核：</label>
        <div class="formControls col-xs-8 col-sm-9">
            @foreach(\App\Components\Utils::VOTE_AUDIT_MODE_VAL as $key=>$value)
                <div class="radio-box">
                    <input type="radio" id="vote_audit_mode_{{$key}}" name="vote_audit_mode"
                           value="{{$key}}" {{strval($key) == 0 ? 'checked' : ''}}>
                    <label for="vote_audit_mode_{{$key}}">{{$value}}</label>
                </div>
            @endforeach
        </div>
    </div>
    <div class="row cl hide">
        <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>最少参与人数：</label>
        <div class="formControls col-xs-8 col-sm-9">
            <input id="apply_min_num" name="apply_min_num" type="number" class="input-text"
                   style="width: 150px;"
                   value="{{ isset($data->apply_min_num) ? $data->apply_min_num : 0 }}" placeholder="最少参与人数">
        </div>
    </div>
    <div class="row cl item c-999 hide">
        <label class="form-label col-xs-4 col-sm-2"></label>
        <div class="formControls col-xs-8 col-sm-9">
            <div>
                <span>达到最小报名人数后，才能进行投票，默认为0，即不控制最小参与人数</span>
            </div>
        </div>
    </div>
    <div class="row cl hide">
        <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>每人每日每用户：</label>
        <div class="formControls col-xs-8 col-sm-9">
            <input id="daily_vote_to_user_num" name="daily_vote_to_user_num" type="number" class="input-text"
                   style="width: 150px;"
                   value="{{ isset($data->daily_vote_to_user_num) ? $data->daily_vote_to_user_num : '2' }}"
                   placeholder="每人每日每用户可以投票">
        </div>
    </div>
    <div class="row cl item c-999 hide">
        <label class="form-label col-xs-4 col-sm-2"></label>
        <div class="formControls col-xs-8 col-sm-9">
            <div>
                <span>每人每日每用户可以投票的数量，默认为每人每日可以给某个参赛选手投2票</span>
            </div>
        </div>
    </div>
    <div class="row cl hide">
        <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>每人每日投票总数：</label>
        <div class="formControls col-xs-8 col-sm-9">
            <input id="daily_vote_num" name="daily_vote_num" type="number" class="input-text"
                   style="width: 150px;"
                   value="{{ isset($data->daily_vote_num) ? $data->daily_vote_num : '2' }}" placeholder="每人每日投票总数">
        </div>
    </div>
    <div class="row cl item c-999 hide">
        <label class="form-label col-xs-4 col-sm-2"></label>
        <div class="formControls col-xs-8 col-sm-9">
            <div>
                <span>每人每日最多可以投票数，默认为每人每日可以最多投2票</span>
            </div>
        </div>
    </div>
    <div class="row cl hide">
        <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>投票消息提醒：</label>
        <div class="formControls col-xs-8 col-sm-9">
            @foreach(\App\Components\Utils::VOTE_NOTICE_MODE_VAL as $key=>$value)
                <div class="radio-box">
                    <input type="radio" id="vote_notice_mode_{{$key}}" name="vote_notice_mode"
                           value="{{$key}}" {{strval($key) == 0 ? 'checked' : ''}}>
                    <label for="vote_notice_mode_{{$key}}">{{$value}}</label>
                </div>
            @endforeach
        </div>
    </div>
    <div class="row cl item c-999 hide">
        <label class="form-label col-xs-4 col-sm-2"></label>
        <div class="formControls col-xs-8 col-sm-9">
            <div>
                <span>通过服务号向参赛选手推送模板消息，此处要求报名选手通过自主报名，因为系统导入的选手数据无法获得用户openid，如有openid可以联系技术组进行数据导入</span>
            </div>
        </div>
    </div>
    <div class="row cl">
        <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>一等奖数量：</label>
        <div class="formControls col-xs-8 col-sm-9">
            <input id="first_prize_num" name="first_prize_num" type="number" class="input-text"
                   style="width: 150px;"
                   value="{{ isset($data->first_prize_num) ? $data->first_prize_num : '1' }}"
                   placeholder="请输入一等奖数量">
        </div>
    </div>
    <div class="row cl">
        <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>二等奖数量：</label>
        <div class="formControls col-xs-8 col-sm-9">
            <input id="second_prize_num" name="second_prize_num" type="number" class="input-text"
                   style="width: 150px;"
                   value="{{ isset($data->second_prize_num) ? $data->second_prize_num : '2' }}"
                   placeholder="请输入二等奖数量">
        </div>
    </div>
    <div class="row cl">
        <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>三等奖数量：</label>
        <div class="formControls col-xs-8 col-sm-9">
            <input id="third_prize_num" name="third_prize_num" type="number" class="input-text"
                   style="width: 150px;"
                   value="{{ isset($data->third_prize_num) ? $data->third_prize_num : '3' }}"
                   placeholder="请输入三等奖数量">
        </div>
    </div>
    <div class="row cl">
        <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>优秀奖数量：</label>
        <div class="formControls col-xs-8 col-sm-9">
            <span class="col-lg-9">优秀奖个数无需设置，超过500票既获得优秀奖</span>
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
                vote_vertify_mode: {
                    required: true,
                },
                vote_audit_mode: {
                    required: true,
                },
                apply_min_num: {
                    required: true,
                },
                daily_vote_to_user_num: {
                    required: true,
                },
                daily_vote_num: {
                    required: true,
                },
                vote_notice_mode: {
                    required: true,
                },
                first_prize_num: {
                    required: true,
                },
                second_prize_num: {
                    required: true,
                },
                third_prize_num: {
                    required: true,
                },
                honor_prize_num: {
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
                    url: "{{ route('team.activity.edit')}}",
                    success: function (ret) {
                        console.log(JSON.stringify(ret));
                        if (ret.result) {
                            layer.msg('保存成功', {icon: 1, time: 1000});
                            location.replace('{{route('team.activity.edit')}}?item={{$item}}&id=' + ret.ret.id);
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
