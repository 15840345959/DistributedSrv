@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 广告图管理 <span
                class="c-gray en">&gt;</span> 广告图列表 <a class="btn btn-success radius r btn-refresh"
                                                       style="line-height:1.6em;margin-top:3px"
                                                       title="刷新"
                                                       onclick="location.replace('{{URL::asset('/admin/shop/shopAD/index')}}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>
    <div class="page-container">

        <div class="cl pd-5 bg-1 bk-gray mt-20">
            <span class="l">
                 <a href="javascript:;"
                    onclick="edit('添加广告图','{{URL::asset('/admin/shop/shopAD/edit')}}')"
                    class="btn btn-primary radius">
                     <i class="Hui-iconfont">&#xe600;</i> 添加广告图
                 </a>
            </span>
            {{--<span class="r">共有数据：<strong>{{$datas->count()}}</strong> 条</span>--}}
        </div>

        <div class="mt-20">
            <table class="table table-border table-bordered table-bg table-sort">
                <thead>
                <tr>
                    <th scope="col" colspan="9">广告图列表</th>
                </tr>
                <tr class="text-c">
                    {{--<th width="25"><input type="checkbox" name="" value=""></th>--}}
                    <th width="40">ID</th>
                    <th width="150">轮播图</th>
                    <th width="180">标题</th>
                    <th width="80">类型</th>
                    <th width="80">跳转目标</th>
                    <th width="50">创建人员</th>
                    <th width="100">广告位置</th>
                    <th width="80">状态</th>
                    <th width="80">操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($datas as $data)
                    <tr class="text-c">
                        {{--<td><input type="checkbox" value="1" name=""></td>--}}
                        <td>{{$data->id}}</td>
                        <td>
                            <img src="{{ $data->img ? $data->img.'?imageView2/1/w/70/h/35/interlace/1/q/75|imageslim' : URL::asset('/img/upload_rect.png')}}">
                        </td>
                        <td>
                            {{$data->title}}
                        </td>
                        <td>
                            <span class="c-primary">{{$data->type_str}}</span>
                        </td>
                        <td>
                            {{isset($data->target)?$data->target:'--'}}
                        </td>
                        <td>
                            {{$data->admin->name}}
                        </td>
                        <td>{{$data->position_str}}</td>
                        <td class="td-status">
                            @if($data->status=="1")
                                <span class="label label-success radius">正常</span>
                            @else
                                <span class="label label-default radius">冻结</span>
                            @endif
                        </td>
                        <td class="td-manage">
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
                               onclick="edit('编辑广告图','{{URL::asset('/admin/shop/shopAD/edit')}}?id={{$data->id}})',{{$data->id}})"
                               class="c-primary ml-5" style="text-decoration:none">
                                编辑
                            </a>
                            {{--<a title="删除" href="javascript:;"--}}
                            {{--onclick="del(this,'{{$data->id}}')"--}}
                            {{--class="ml-5 c-primary"--}}
                            {{--style="text-decoration:none">--}}
                            {{--删除--}}
                            {{--</a>--}}
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
        /*广告图-增加*/
        function edit(title, url) {
            var index = layer.open({
                type: 2,
                title: title,
                content: url
            });
            layer.full(index);
        }

        /*广告图-删除*/
        function del(obj, id) {
            layer.alert('不能删除轮播图，否则将导致数据混乱，请联系技术团队 TerryQi负责');
        }

        /*广告图-停用*/
        function stop(obj, id) {
            consoledebug.log("stop id:" + id);
            layer.confirm('确认要停用吗？', function (index) {
                //此处请求后台程序，下方是成功后的前台处理
                var param = {
                    id: id,
                    status: 0,
                    _token: "{{ csrf_token() }}"
                }
                //从后台设置广告图状态
                shop_shopAD_setStatus('{{URL::asset('')}}', param, function (ret) {
                    if (ret.status == true) {

                    }
                })
                $(obj).parents("tr").find(".td-manage").prepend('<a onClick="start(this,' + id + ')" href="javascript:;" title="启用" class="c-primary" style="text-decoration:none">启用</a>');
                $(obj).parents("tr").find(".td-status").html('<span class="label label-default radius">冻结</span>');
                $(obj).remove();
                layer.msg('已停用', {icon: 5, time: 1000});
            });
        }

        /*广告图-启用*/
        function start(obj, id) {
            layer.confirm('确认要启用吗？', function (index) {
                //此处请求后台程序，下方是成功后的前台处理
                var param = {
                    id: id,
                    status: 1,
                    _token: "{{ csrf_token() }}"
                }
                //从后台设置广告图状态
                shop_shopAD_setStatus('{{URL::asset('')}}', param, function (ret) {
                    if (ret.status == true) {

                    }
                })
                $(obj).parents("tr").find(".td-manage").prepend('<a onClick="stop(this,' + id + ')" href="javascript:;" title="停用" class="c-primary" style="text-decoration:none">停用</a>');
                $(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">正常</span>');
                $(obj).remove();
                layer.msg('正常', {icon: 6, time: 1000});
            });
        }


    </script>
@endsection