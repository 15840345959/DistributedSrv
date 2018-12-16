@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 2B业务管理 <span
                class="c-gray en">&gt;</span> 消息通知列表 <a class="btn btn-success radius r btn-refresh"
                                                        style="line-height:1.6em;margin-top:3px"
                                                        title="刷新"
                                                        onclick="location.replace('{{route('admin.b.message.index', $con_arr)}}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>
    <div class="page-container">

        <div class="text-c">
            <form id="search_form" action="{{route('admin.b.message.index')}}" method="post"
                  class="form-horizontal">
                {{csrf_field()}}
                <div class="Huiform text-r">
                    <span>按标题搜索： </span>
                    <input id="search_word" name="search_word" type="text" class="input-text" style="width:100px"
                           placeholder="按标题搜索" value="{{$con_arr['search_word']}}">
                    <span class="ml-5">业务名称： </span>
                    <span class="select-box" style="width:150px">
                        <select class="select" name="busi_name" id="busi_name" size="1">
                            <option value="" {{$con_arr['busi_name']==""?'selected':''}}>请选择</option>
                            @foreach(\App\Components\Utils::BUSI_NAME_VAL as $key=>$value)
                                <option value="{{$key}}" {{$con_arr['busi_name']==strval($key)?'selected':''}}>{{$value}}</option>
                            @endforeach
                        </select>
                    </span>
                    <span class="ml-5">级别： </span>
                    <span class="select-box" style="width:150px">
                        <select class="select" name="level" id="level" size="1">
                            <option value="" {{$con_arr['level']==""?'selected':''}}>请选择</option>
                            @foreach(\App\Components\Utils::TO_B_MESSAGE_LEVEL as $key=>$value)
                                <option value="{{$key}}" {{$con_arr['level']==strval($key)?'selected':''}}>{{$value}}</option>
                            @endforeach
                        </select>
                    </span>

                    <button type="submit" class="btn btn-success" id="" name="">
                        <i class="Hui-iconfont">&#xe665;</i> 搜索
                    </button>
                </div>
            </form>
        </div>

        <div class="cl pd-5 bg-1 bk-gray mt-20">
            <span class="l">
                 <a href="javascript:;"
                    onclick="edit('添加消息通知','{{route('admin.b.message.edit')}}?busi_name={{$con_arr['busi_name']}}')"
                    class="btn btn-primary radius">
                     <i class="Hui-iconfont">&#xe600;</i> 添加消息通知
                 </a>
            </span>
            {{--<span class="r">共有数据：<strong>{{$datas->count()}}</strong> 条</span>--}}
        </div>

        <div class="mt-20">
            <table class="table table-border table-bordered table-bg table-sort">
                <thead>
                <tr>
                    <th scope="col" colspan="10">消息通知列表</th>
                </tr>
                <tr class="text-c">
                    {{--<th width="25"><input type="checkbox" name="" value=""></th>--}}
                    <th width="40">ID</th>
                    <th width="350">标题</th>
                    <th width="50">业务名称</th>
                    {{--<th width="50">规则位置</th>--}}
                    <th width="50">消息类型</th>
                    <th width="50">消息级别</th>
                    <th width="50">排序值</th>
                    <th width="50">创建人员</th>
                    <th width="100">创建时间</th>
                    <th width="50">状态</th>
                    <th width="80">操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($datas as $data)
                    <tr class="text-c">
                        {{--<td><input type="checkbox" value="1" name=""></td>--}}
                        <td>{{$data->id}}</td>
                        <td>
                            {{$data->title}}
                        </td>
                        <td>
                            {{$data->busi_name_str}}
                        </td>
                        {{--<td>--}}
                            {{--{{$data->position_str}}--}}
                        {{--</td>--}}
                        <td>
                            {{$data->type_str}}
                        </td>
                        <td>
                            @if ($data->level == '0')
                                <span class="label label-default radius">{{$data->level_str}}</span>
                            @elseif ($data->level == '1')
                                <span class="label label-warning radius">{{$data->level_str}}</span>
                            @elseif ($data->level == '2')
                                <span class="label label-danger radius">{{$data->level_str}}</span>
                            @endif

                        </td>
                        {{--<td>--}}
                            {{--@if($data->top =="1")--}}
                                {{--<span class="label label-success radius">是</span>--}}
                            {{--@else--}}
                                {{--<span class="label label-default radius">否</span>--}}
                            {{--@endif--}}
                        {{--</td>--}}
                        <td>
                            {{$data->seq}}
                        </td>
                        <td>
                            {{$data->admin->name}}
                        </td>
                        <td>{{$data->created_at}}</td>
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
                               onclick="edit('编辑活动规则','{{route('admin.b.message.edit')}}?id={{$data->id}})',{{$data->id}})"
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
        /*活动规则-增加*/
        function edit(title, url) {
            var index = layer.open({
                type: 2,
                title: title,
                content: url
            });
            layer.full(index);
        }

        /*活动规则-删除*/
        function del(obj, id) {
            layer.alert('不能删除礼品，否则将导致数据混乱，请联系技术团队 TerryQi负责');
        }

        /*活动规则-停用*/
        function stop(obj, id) {
            consoledebug.log("stop id:" + id);
            layer.confirm('确认要停用吗？', function (index) {
                //此处请求后台程序，下方是成功后的前台处理
                var param = {
                    id: id,
                    status: 0,
                    _token: "{{ csrf_token() }}"
                }
                //从后台设置活动规则状态
                message_setStatus('{{route('admin.b.message.setStatus')}}', param, function (ret) {
                    if (ret.status == true) {

                    }
                })
                $(obj).parents("tr").find(".td-manage").prepend('<a onClick="start(this,' + id + ')" href="javascript:;" title="启用" class="c-primary" style="text-decoration:none">启用</a>');
                $(obj).parents("tr").find(".td-status").html('<span class="label label-default radius">冻结</span>');
                $(obj).remove();
                layer.msg('已停用', {icon: 5, time: 1000});
            });
        }

        /*活动规则-启用*/
        function start(obj, id) {
            layer.confirm('确认要启用吗？', function (index) {
                //此处请求后台程序，下方是成功后的前台处理
                var param = {
                    id: id,
                    status: 1,
                    _token: "{{ csrf_token() }}"
                }
                //从后台设置活动规则状态
                message_setStatus('{{route('admin.b.message.setStatus')}}', param, function (ret) {
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