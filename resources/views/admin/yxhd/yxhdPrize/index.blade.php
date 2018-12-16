@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 奖品管理 <span
                class="c-gray en">&gt;</span> 奖品列表 <a class="btn btn-success radius r btn-refresh"
                                                      style="line-height:1.6em;margin-top:3px"
                                                      title="刷新"
                                                      onclick="location.replace('{{URL::asset('/admin/yxhd/yxhdPrize/index')}}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>
    <div class="page-container">

        <div class="text-c">
            <form id="search_form" action="{{URL::asset('/admin/yxhd/yxhdPrize/index')}}" method="post"
                  class="form-horizontal">
                {{csrf_field()}}
                <div class="Huiform text-r">
                    <input id="search_word" name="search_word" type="text" class="input-text" style="width:250px"
                           placeholder="根据名称或关键词称进行检索" value="{{$con_arr['search_word']}}">
                    <input id="id" name="id" type="text" class="input-text" style="width:150px"
                           placeholder="奖品id" value="{{$con_arr['id']}}">
                    <button type="submit" class="btn btn-success" id="" name="">
                        <i class="Hui-iconfont">&#xe665;</i> 搜索
                    </button>
                </div>
            </form>
        </div>

        <div class="cl pd-5 bg-1 bk-gray mt-20">
            <span class="l">
                 <a href="javascript:;"
                    onclick="edit('新建奖品','{{URL::asset('/admin/yxhd/yxhdPrize/edit')}}')"
                    class="btn btn-primary radius">
                     <i class="Hui-iconfont">&#xe600;</i> 添加奖品
                 </a>
            </span>
        </div>

        <div class="mt-20">
            <table class="table table-border table-bordered table-bg table-sort">
                <thead>
                <tr>
                    <th scope="col" colspan="9">奖品列表
                        <span class="r">共有数据：<strong>{{$datas->total()}}</strong> 条</span></th>
                </tr>
                <tr class="text-c">
                    {{--<th width="25"><input type="checkbox" name="" value=""></th>--}}
                    <th width="40">ID</th>
                    <th width="250">名称</th>
                    <th width="100">图片</th>
                    <th width="100">类型</th>
                    <th width="50">库存</th>
                    <th width="50">已派发</th>
                    <th width="50">状态</th>
                    <th width="50">管理员</th>
                    <th width="80">操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($datas as $data)
                    <tr class="text-c">
                        <td>
                            <span>{{$data->id}}</span>
                        </td>
                        <td>
                            <span class="c-primary">{{$data->name}}</span>
                        </td>
                        <td>
                            <img src="{{$data->img}}" style="width: 40px;">
                        </td>
                        <td>
                            <span class="c-primary">{{$data->type_str}}</span>
                        </td>
                        <td class="">
                            <span>{{$data->total_num}}</span>
                        </td>
                        <td class="">
                            <span>{{$data->send_num}}</span>
                        </td>
                        <td class="">
                            @if($data->status=="1")
                                <span class="label label-success radius">{{$data->status_str}}</span>
                            @else
                                <span class="label label-default radius">{{$data->status_str}}</span>
                            @endif
                        </td>
                        <td class="">
                            <span>{{$data->admin->name}}</span>
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
                                   onclick="edit('编辑奖品-{{$data->name}}','{{URL::asset('/admin/yxhd/yxhdPrize/edit')}}?id={{$data->id}})',{{$data->id}})"
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
        /*奖品-增加*/
        function edit(title, url) {
            var index = layer.open({
                type: 2,
                title: title,
                content: url
            });
            layer.full(index);
        }


        /*奖品-停用*/
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
                //从后台设置奖品状态
                /*
                 * 2018-12-10 目前感觉这样的方式更好，因为此类功能只有这里有提现，所以不需要去该common.js
                 *
                 * By TerryQi
                 *
                 * 等待时间的验证
                 */
                ajaxRequest('{{URL::asset('')}}' + "admin/yxhd/yxhdPrize/setStatus/" + param.id, param, "GET", function (ret) {
                    if (ret.result == true) {
                        layer.msg('已停用', {icon: 5, time: 1000});
                        $("#search_form").submit();
                    } else {
                        layer.msg(ret.message, {icon: 5, time: 2000});
                    }
                    layer.close(index);
                });
            });
        }

        /*奖品-启用*/
        function start(obj, id) {
            layer.confirm('确认要启用吗？', function (index) {
                //此处请求后台程序，下方是成功后的前台处理
                var param = {
                    id: id,
                    status: 1,
                    _token: "{{ csrf_token() }}"
                }

                var index = layer.load(2, {time: 10 * 1000}); //加载
                //从后台设置奖品状态
                ajaxRequest('{{URL::asset('')}}' + "admin/yxhd/yxhdPrize/setStatus/" + param.id, param, "GET", function (ret) {
                    if (ret.result == true) {
                        layer.msg('已启用', {icon: 1, time: 1000});
                        $("#search_form").submit();
                    } else {
                        layer.msg(ret.message, {icon: 5, time: 2000});
                    }
                    layer.close(index);
                });
            });
        }


    </script>
@endsection