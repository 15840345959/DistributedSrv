@extends('admin.layouts.app')

@section('content')

    <div class="page-container">
        <form class="form form-horizontal" id="form-edit">
            {{csrf_field()}}
            <div class="row cl hidden">
                <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>菜单id：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input id="id" name="id" type="text" class="input-text "
                           value="{{ isset($data->id) ? $data->id : '' }}" placeholder="菜单id" readonly
                           style="width: 400px;background-color: rgb(235, 235, 228);">
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
                <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>菜单名称：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input id="name" name="name" type="text" class="input-text"
                           value="{{ isset($data->name) ? $data->name : '' }}" placeholder="请输入菜单名称"
                           style="width: 400px;">
                </div>
            </div>
            <div class="row cl level-item">
                <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>是否有下级菜单：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <span class="select-box" style="width: 400px;">
                        <select id="level" name="level" class="select" onchange="selectLevel()">
                            <option value="0" {{$data->level=='0'?'selected':''}}>无下级菜单</option>
                            <option value="1" {{$data->level=='1'?'selected':''}}>有下级菜单</option>
                        </select>
                    </span>
                </div>
            </div>
            <div class="row cl item f-id-item">
                <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>父级菜单：</label>
                <div class="formControls col-xs-8 col-sm-9">
                     <span class="select-box" style="width: 400px;">
                         <select id="f_id" name="f_id" class="select">
                             <option value="0">无父级菜单</option>
                             @foreach($f_menus as $f_menu)
                                 <option value="{{$f_menu->id}}" {{$data->f_id==$f_menu->id?'selected':''}}>{{$f_menu->name}}</option>
                             @endforeach
                         </select>
                     </span>
                </div>
            </div>

            <div class="row cl item type-item">
                <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>菜单类型：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <span class="select-box" style="width: 400px;">
                        <select id="type" name="type" class="select" onchange="selectType()">
                            @foreach(\App\Components\Utils::MENU_TYPE_VAL as $key=>$value)
                                <option value="{{$key}}" {{$data->type==strval($key)?'selected':''}}>{{$value}}</option>
                            @endforeach
                        </select>
                    </span>
                </div>
            </div>
            <div class="row cl item url-item">
                <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>链接：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input id="url" name="url" type="text" class="input-text"
                           value="{{ isset($data->url) ? $data->url : '' }}" placeholder="请输入链接"
                           style="width: 400px;">
                </div>
            </div>
            <div class="row cl item url-item c-999">
                <label class="form-label col-xs-4 col-sm-2"></label>
                <div class="formControls col-xs-8 col-sm-9">
                    <div>
                        <span>链接形式为：http://s.isart.me/</span>
                    </div>
                </div>
            </div>
            <div class="row cl item media-id-item">
                <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>素材media_id：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input id="media_id" name="media_id" type="text" class="input-text"
                           value="{{ isset($data->media_id) ? $data->media_id : '' }}" placeholder="素材id"
                           style="width: 400px;">
                </div>
            </div>
            <div class="row cl item media-id-item c-999">
                <label class="form-label col-xs-4 col-sm-2"></label>
                <div class="formControls col-xs-8 col-sm-9">
                    <div>
                        <span>较低版本微信的小程序将打开该该URL链接</span>
                    </div>
                </div>
            </div>
            <div class="row cl item key-item">
                <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>事件配置：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input id="key" name="key" type="text" class="input-text"
                           value="{{ isset($data->key) ? $data->key : '' }}" placeholder="事件配置"
                           style="width: 400px;">
                </div>
            </div>
            <div class="row cl item key-item c-999">
                <label class="form-label col-xs-4 col-sm-2"></label>
                <div class="formControls col-xs-8 col-sm-9">
                    <div>
                        <span>事件配置需要定制开发，请联系开发人员处理</span>
                    </div>
                </div>
            </div>

            <div class="row cl item appid-item">
                <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>小程序appid：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input id="appid" name="appid" type="text" class="input-text"
                           value="{{ isset($data->appid) ? $data->appid : '' }}" placeholder="请输入小程序appid"
                           style="width: 400px;">
                </div>
            </div>
            <div class="row cl item c-999 appid-item">
                <label class="form-label col-xs-4 col-sm-2"></label>
                <div class="formControls col-xs-8 col-sm-9">
                    <div>
                        <span>小程序的appid（仅认证公众号可配置）</span>
                    </div>
                </div>
            </div>

            <div class="row cl item pagepath-item">
                <label class="form-label cpagepath-itemol-xs-4 col-sm-2"><span class="c-red">*</span>小程序页面路径：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input id="pagepath" name="pagepath" type="text" class="input-text"
                           value="{{ isset($data->pagepath) ? $data->pagepath : '' }}" placeholder="请输入小程序页面路径"
                           style="width: 400px;">
                </div>
            </div>
            <div class="row cl item c-999 pagepath-item">
                <label class="form-label col-xs-4 col-sm-2"></label>
                <div class="formControls col-xs-8 col-sm-9">
                    <div>
                        <span>小程序的页面路径</span>
                    </div>
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>排序：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input id="seq" name="seq" type="number" class="input-text"
                           value="{{ isset($data->seq) ? $data->seq : 0 }}" placeholder="请输入排序,越大越靠前"
                           style="width: 400px;">
                </div>
            </div>
            <div class="row cl item c-999">
                <label class="form-label col-xs-4 col-sm-2"></label>
                <div class="formControls col-xs-8 col-sm-9">
                    <div>
                        <span>值越大越靠前</span>
                    </div>
                </div>
            </div>
            <div class="row cl mt-20">
                <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-2">
                    <button class="btn btn-primary radius" type="submit"><i class="Hui-iconfont">&#xe632;</i> 保存
                    </button>
                    <button onClick="layer_close();" class="btn btn-default radius" type="button">取消</button>
                </div>
            </div>
            <div class="row cl c-999">
                <label class="form-label col-xs-4 col-sm-2">菜单配置说明</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <div>
                        <span>自定义菜单最多包括3个一级菜单，每个一级菜单最多包含5个二级菜单。</span>
                    </div>
                    <div>
                        <span>一级菜单最多4个汉字，二级菜单最多7个汉字，多出来的部分将会以“...”代替。</span>
                    </div>
                    <div>
                        <span>创建自定义菜单后，菜单的刷新策略是，在用户进入公众号会话页或公众号profile页时，如果发现上一次拉取菜单的请求在5分钟以前，就会拉取一下菜单，如果菜单有更新，就会刷新客户端的菜单。测试时可以尝试取消关注公众账号后再次关注，则可以看到创建后的效果。</span>
                    </div>
                </div>
            </div>
        </form>
    </div>

@endsection

@section('script')
    <script type="text/javascript">

        $(function () {

            //设置菜单级别
            selectLevel();

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
                        url: "{{ URL::asset('/admin/gzh/menu/edit')}}",
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


        //选中level
        function selectLevel() {
            console.log("selectLevel");
            var level = $("#level").val();
            console.log("level:" + level);
            $(".item").addClass('hidden');
            switch (level) {
                case '0':      //无下级菜单，可以有配置项
                    console.log("type-item removeClass hidden");
                    $(".type-item").removeClass('hidden');
                    $(".f-id-item").removeClass('hidden');
                    selectType();       //配置菜单项目
                    break;
                case '1':     //有下级菜单

                    break;

            }
        }

        //选择菜单类型
        function selectType() {
            console.log("selectType");
            var type = $("#type").val();
            console.log("type:" + type);
            // 隐藏全部非必选项，根据type不同进行显示
            $(".item").addClass('hidden');
            //但不影响type选项
            $(".type-item").removeClass('hidden');
            $(".f-id-item").removeClass('hidden');

            $(".f-id-item").removeClass('hidden');
            switch (type) {
                case "click":
                    $(".key-item").removeClass('hidden');
                    break;
                case "view":
                    $(".url-item").removeClass('hidden');
                    break;
                case "media_id":
                    $(".media-id-item").removeClass('hidden');
                    break;
                case "miniprogram":
                    $(".url-item").removeClass('hidden');
                    $(".appid-item").removeClass('hidden');
                    $(".pagepath-item").removeClass('hidden');
                    break;
            }
        }


    </script>
@endsection

























