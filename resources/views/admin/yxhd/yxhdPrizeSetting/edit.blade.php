@extends('admin.layouts.app')

@section('content')

    <div class="page-container">
        <form class="form form-horizontal" id="form-edit">
            {{csrf_field()}}
            <div class="row cl hidden">
                <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>活动id：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input id="activity_id" name="activity_id" type="text" class="input-text"
                           value="{{ isset($yxhdActivity->id) ? $yxhdActivity->id : '' }}" placeholder="活动id">
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>活动名称：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input id="activity_name" name="activity_name" type="text" class="input-text"
                           value="{{ isset($yxhdActivity->name) ? $yxhdActivity->name : '' }}" disabled>
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>选择奖品：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <span class="select-box" style="width: 200px;">
                        <select id="prize_id" name="prize_id" class="select">
                            <option value="">请选择</option>
                            @foreach($yxhdPrizes as $yxhdPrize)
                                <option value="{{$yxhdPrize->id}}" {{$yxhdPrize->id==$data->prize_id?'selected':''}}>{{$yxhdPrize->name}}</option>
                            @endforeach
                        </select>
                    </span>
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2">中奖概率：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input id="rate" name="rate" type="text" class="input-text" style="width: 200px;"
                           value="{{ isset($data->rate) ? $data->rate : '0' }}"> <span
                            class="ml-5">%</span>
                </div>
            </div>
            <div class="row cl item c-999">
                <label class="form-label col-xs-4 col-sm-2"></label>
                <div class="formControls col-xs-8 col-sm-9">
                    <div>
                        <span class="c-danger">请注意中奖概率为百分比，可以输入10，代表10%，输入0.01代表0.01%</span>
                    </div>
                </div>
            </div>

            <div class="row cl mt-20">
                <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-2">
                    <button class="btn btn-primary radius" type="submit"><i class="Hui-iconfont">&#xe632;</i> 配置奖品信息
                    </button>
                </div>
            </div>
        </form>
    </div>


@endsection

@section('script')

    <script type="text/javascript">

        $(function () {
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
                        url: "{{ URL::asset('/admin/yxhd/yxhdPrizeSetting/edit')}}",
                        success: function (ret) {
                            console.log(JSON.stringify(ret));
                            if (ret.result) {
                                layer.msg('设置成功', {icon: 1, time: 1000});
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

    </script>
@endsection