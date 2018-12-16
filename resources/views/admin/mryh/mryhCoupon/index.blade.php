@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 优惠券管理 <span
                class="c-gray en">&gt;</span> 优惠券列表 <a class="btn btn-success radius r btn-refresh"
                                                       style="line-height:1.6em;margin-top:3px"
                                                       title="刷新"
                                                       onclick="location.replace('{{URL::asset('/admin/mryh/mryhCoupon/index')}}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>
    <div class="page-container">

        <div class="text-c">
            <form id="search_form"  action="{{URL::asset('/admin/mryh/mryhCoupon/index')}}" method="post" class="form-horizontal">
                {{csrf_field()}}
                <div class="Huiform text-r">
                    <input id="search_word" name="search_word" type="text" class="input-text" style="width:250px"
                           placeholder="根据名称或关键词称进行检索" value="{{$con_arr['search_word']}}">
                    <button type="submit" class="btn btn-success" id="" name="">
                        <i class="Hui-iconfont">&#xe665;</i> 搜索
                    </button>
                </div>
            </form>
        </div>

        <div class="cl pd-5 bg-1 bk-gray mt-20">
            <span class="l">
                 <a href="javascript:;"
                    onclick="edit('新建优惠券','{{URL::asset('/admin/mryh/mryhCoupon/edit')}}')"
                    class="btn btn-primary radius">
                     <i class="Hui-iconfont">&#xe600;</i> 添加优惠券
                 </a>
            </span>
        </div>

        <div class="mt-20">
            <table class="table table-border table-bordered table-bg table-sort">
                <thead>
                <tr>
                    <th scope="col" colspan="5">优惠券列表
                        <span class="r">共有数据：<strong>{{$datas->total()}}</strong> 条</span></th>
                </tr>
                <tr class="text-c">
                    {{--<th width="25"><input type="checkbox" name="" value=""></th>--}}
                    <th width="100">优惠券名称</th>
                    <th width="100">介绍信息</th>
                    <th width="100">规则</th>
                    <th width="80">状态</th>
                    <th width="80">操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($datas as $data)
                    <tr class="text-c">
                        <td>
                            <div>
                                <img src="{{$data->img}}" style="width: 80px;">
                            </div>
                            <div class="mt-5">
                                <span>{{$data->name}}</span>
                            </div>
                            <div class="mt-5">
                                <span class="c-primary">优惠券编号：{{$data->id}}/{{$data->code}}</span>
                            </div>
                            <div class="mt-5">
                                <span>创建时间：{{$data->created_at}}</span>
                            </div>
                            <div class="mt-5">
                                <span>创建管理员：{{$data->admin->name}}</span>
                            </div>
                        </td>
                        <td>
                            <div>
                                <span>{{$data->intro_text}}</span>
                            </div>
                        </td>
                        <td>
                            <div>
                                <span>起算日期</span><span class="ml-5 c-primary">{{$data->con_date}}</span>
                            </div>
                            <div class="mt-5">
                                <span>拉新目标</span><span class="ml-5 label label-primary">{{$data->con_yq_num}}</span>
                            </div>
                            <div class="mt-5">
                                <span>有效天数</span><span class="ml-5 label label-success">{{$data->con_valid_days}}</span>
                            </div>
                        </td>
                        <td class="td-status">
                            <div>
                                @if($data->status=="1")
                                    <span class="label label-success radius">正常</span>
                                @else
                                    <span class="label label-default radius">冻结</span>
                                @endif
                            </div>
                            <div class="mt-5">
                                <span class="label label-success radius">{{$data->type_str}}</span>
                            </div>
                        </td>
                        <td class="td-manage">

                            <div>
                                @if($data->status=="1")
                                    <a style="text-decoration:none" onClick="stop(this,'{{$data->id}}')"
                                       href="javascript:;" class="c-primary"
                                       title="停用">
                                        停用
                                    </a>
                                @else
                                    <a style="text-decoration:none" onClick="start(this,'{{$data->id}}')"
                                       href="javascript:;" class="c-primary"
                                       title="启用">
                                        启用
                                    </a>
                                @endif
                                <a title="编辑" href="javascript:;"
                                   onclick="edit('编辑优惠券-{{$data->name}}','{{URL::asset('/admin/mryh/mryhCoupon/edit')}}?id={{$data->id}})',{{$data->id}})"
                                   class="c-primary ml-5" style="text-decoration:none">
                                    编辑
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="mt-20">
                {{ $datas->appends($con_arr)->links() }}
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">


        $(function () {

        });

        /*
         参数解释：
         title	标题
         url		请求的url
         id		需要操作的数据id
         w		弹出层宽度（缺省调默认值）
         h		弹出层高度（缺省调默认值）
         */
        /*优惠券-增加*/
        function edit(title, url) {
            creatIframe(url, title)
        }

        /*优惠券-删除*/
        function del(obj, id) {
            layer.alert('不能删除优惠券，否则将导致数据混乱，请联系技术团队 TerryQi负责');
        }

        /*优惠券-停用*/
        function stop(obj, id) {
            consoledebug.log("stop id:" + id);
            layer.confirm('确认要停用吗？', function (index) {
                //此处请求后台程序，下方是成功后的前台处理
                var param = {
                    id: id,
                    status: 0,
                    _token: "{{ csrf_token() }}"
                }
                var index = layer.load(2, {time: 10 * 1000}); //加载
                //从后台设置优惠券状态
                mryh_mryhCoupon_setStatus('{{URL::asset('')}}', param, function (ret) {
                    if (ret.result == true) {
                        layer.msg('已停用', {icon: 5, time: 1000});
                        $("#search_form").submit();
                    } else {
                        layer.msg(ret.message, {icon: 5, time: 2000});
                    }
                    layer.close(index);
                })

            });
        }

        /*优惠券-启用*/
        function start(obj, id) {
            layer.confirm('确认要启用吗？', function (index) {
                //此处请求后台程序，下方是成功后的前台处理
                var param = {
                    id: id,
                    status: 1,
                    _token: "{{ csrf_token() }}"
                }

                var index = layer.load(2, {time: 10 * 1000}); //加载
                //从后台设置优惠券状态
                mryh_mryhCoupon_setStatus('{{URL::asset('')}}', param, function (ret) {
                    if (ret.result == true) {
                        layer.msg('正常', {icon: 6, time: 1000});
                        $("#search_form").submit();
                    } else {
                        layer.msg(ret.message, {icon: 5, time: 2000});
                    }
                    layer.close(index);
                })

            });
        }

    </script>
@endsection