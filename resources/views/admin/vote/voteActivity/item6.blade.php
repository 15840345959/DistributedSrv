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
        <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>广告模式：</label>
        <div class="formControls col-xs-8 col-sm-9">
            @foreach(\App\Components\Utils::VOTE_SHOW_AD_MODE_VAL as $key=>$value)
                <div class="radio-box">
                    <input type="radio" id="show_ad_mode_{{$key}}" name="show_ad_mode"
                           value="{{$key}}" {{$data->show_ad_mode==strval($key)?'checked':''}}>
                    <label for="show_ad_mode_{{$key}}">{{$value}}</label>
                </div>
            @endforeach
        </div>
    </div>
    <div class="row cl item c-999">
        <label class="form-label col-xs-4 col-sm-2"></label>
        <div class="formControls col-xs-8 col-sm-9">
            <div>
                <span>启用倒计时广告后，系统将判断活动是否达到倒计时条件，若果达到倒计时，将在轮播图管理中获取最新的倒计时广告并展示，即模式为倒计时的广告</span>
            </div>
        </div>
    </div>

    <div class="row cl item">
        <label class="form-label col-xs-4 col-sm-2">首屏首位广告：</label>
        <div class="formControls col-xs-8 col-sm-9">
            <input id="index_ad_img" name="index_ad_img" type="text" class="input-text" style=""
                   value="{{ isset($data->index_ad_img) ? $data->index_ad_img : ''}}"
                   placeholder="请输入选择图片">
            <div id="container" class="margin-top-10">
                <img id="pickfiles"
                     src="{{ isset($data->index_ad_img) ? $data->index_ad_img : URL::asset('/img/upload.png') }}"
                     style="width: 350px;">
            </div>
            <div style="font-size: 12px;margin-top: 10px;" class="c-999">*请上传800*600比例尺寸图片</div>
        </div>
    </div>
    <div class="row cl item c-999">
        <label class="form-label col-xs-4 col-sm-2"></label>
        <div class="formControls col-xs-8 col-sm-9">
            <div>
                <span>首屏首位广告即首页轮播图广告中，排位第一的广告，该广告通常为机构广告，每个活动都不同。该广告位非必填，如果没有该广告位可以不填写</span>
            </div>
        </div>
    </div>
    <div class="row cl item">
        <label class="form-label col-xs-4 col-sm-2">首位广告链接：</label>
        <div class="formControls col-xs-8 col-sm-9">
            <input id="index_ad_url" name="index_ad_url" type="text" class="input-text" style=""
                   value="{{ isset($data->index_ad_url) ? $data->index_ad_url : ''}}"
                   placeholder="请输入首屏首位广告链接">
        </div>
    </div>
    <div class="row cl item c-999">
        <label class="form-label col-xs-4 col-sm-2"></label>
        <div class="formControls col-xs-8 col-sm-9">
            <div>
                <span>此处输入广告链接，链接样例：https://www.baidu.com/</span>
            </div>
        </div>
    </div>
    <div class="row cl">
        <label class="form-label col-xs-4 col-sm-2">首页广告：</label>
        <div class="formControls col-xs-8 col-sm-9">
            <input id="sel_index_ad_ids" name="sel_index_ad_ids" type="text" class="input-text"
                   value="{{ isset($data->sel_index_ad_ids) ? $data->sel_index_ad_ids : '' }}" placeholder="请输入首页广告id">
        </div>
    </div>
    <div class="row cl item c-999">
        <label class="form-label col-xs-4 col-sm-2"></label>
        <div class="formControls col-xs-8 col-sm-9">
            <div>
                <span>此处的广告列表输出格式：广告1id,广告2id...,即1,3,5或5,3,1，注意用英文,号进行分割id，请注意广告状态，要求广告必须是正常状态</span>
                <br>
                <span style="color: red;">新增: </span><span> 自动倒计时banner 用法为：广告设置中开启倒计时广告(默认顺序为第一张)，若想调整倒计时显示位置，则在首页广告输入框中填写增加 占位符 0。
</span>
                <br>
                <span>示例： 如填写 3,0,4 则倒计时banner为第二张显示。如填写 3,4,0 则倒计时banner为第三张显示。</span>
                <br>
                <span>注注注：不填写占位符 0 的情况实际则为 0,3,4</span>
                <a href="javascript:;"
                   class="" style="text-decoration:none"
                   onclick="creatIframe('{{URL::asset('/admin/vote/voteAD/index')}}','广告列表')">
                    <span class="c-primary">查看广告列表</span>
                </a>
            </div>
        </div>
    </div>
    {{--如果已经选中广告--}}
    @if($data->sel_index_ads)
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2"></label>
            <div class="formControls col-xs-8 col-sm-9">
                <div class="row">
                    @foreach($data->sel_index_ads as $ads)
                        <div class="col-xs-3 text-c">
                            <div>
                                <img src="{{$ads->img}}" style="width: 100px;">
                            </div>
                            <div>
                                <span class="mt-10">{{$ads->title}}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div class="row cl">
        <label class="form-label col-xs-4 col-sm-2">排名页广告：</label>
        <div class="formControls col-xs-8 col-sm-9">
            <input id="sel_pm_ad_ids" name="sel_pm_ad_ids" type="text" class="input-text"
                   value="{{ isset($data->sel_pm_ad_ids) ? $data->sel_pm_ad_ids : '' }}" placeholder="请输入排名页广告id">
        </div>
    </div>
    {{--如果已经选中广告--}}
    @if($data->sel_pm_ads)
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2"></label>
            <div class="formControls col-xs-8 col-sm-9">
                <div class="row">
                    @foreach($data->sel_pm_ads as $ads)
                        <div class="col-xs-3 text-c">
                            <div>
                                <img src="{{$ads->img}}" style="width: 100px;">
                            </div>
                            <div>
                                <span class="mt-10">{{$ads->title}}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
    <div class="row cl">
        <label class="form-label col-xs-4 col-sm-2">投票后弹出屏广告：</label>
        <div class="formControls col-xs-8 col-sm-9">
            <input id="sel_tp_ad_ids" name="sel_tp_ad_ids" type="text" class="input-text"
                   value="{{ isset($data->sel_tp_ad_ids) ? $data->sel_tp_ad_ids : '' }}" placeholder="请输入投票后弹出屏广告id">
        </div>
    </div>
    <div class="row cl">
        <label class="form-label col-xs-4 col-sm-2">弹出屏广告跳转链接：</label>
        <div class="formControls col-xs-8 col-sm-9">
            <input id="sel_tp_ad_url" name="sel_tp_ad_url" type="text" class="input-text"
                   value="{{ isset($data->sel_tp_ad_url) ? $data->sel_tp_ad_url : '' }}" placeholder="请输入投票后弹出屏广告的跳转链接">
        </div>
    </div>
    <div class="row cl">
        <label class="form-label col-xs-4 col-sm-2"></label>
        <div class="formControls col-xs-8 col-sm-9">
            <span class="c-danger">*现阶段投票链接跳转大转盘活动，该url在营销活动管理中查询。</span>
        </div>
    </div>
    {{--如果已经选中广告--}}
    @if($data->sel_tp_ads)
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2"></label>
            <div class="formControls col-xs-8 col-sm-9">
                <div class="row">
                    @foreach($data->sel_tp_ads as $ads)
                        <div class="col-xs-3 text-c">
                            <div>
                                <img src="{{$ads->img}}" style="width: 100px;">
                            </div>
                            <div>
                                <span class="mt-10">{{$ads->title}}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

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
            rules: {},
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
                    $("#index_ad_img").val(sourceLink);
                    //音频播放
                    $("#pickfiles").attr("src", sourceLink);//更新url
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
