@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 管理员管理 <span
                class="c-gray en">&gt;</span> 管理员列表 <a class="btn btn-success radius r btn-refresh"
                                                       style="line-height:1.6em;margin-top:3px"
                                                       href="javascript:location.replace(location.href);" title="刷新"
                                                       onclick="location.replace('{{URL::asset('admin/admin/index')}}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>
    <div class="page-container">
        <div class="text-c">
            <form id="search_form" action="{{URL::asset('admin/admin/index')}}" method="post" class="form-horizontal">
                {{csrf_field()}}
                <div class="Huiform text-r">
                    <input id="search_word" name="search_word" type="text" class="input-text" style="width:350px"
                           placeholder="管理员姓名/手机号码" value="{{$con_arr['search_word']?$con_arr['search_word']:''}}">
                    <span class="select-box" style="width:150px">
                        <select class="select" name="role" id="role" size="1">
                            <option value="" {{$con_arr['role']==""?'selected':''}}>全部角色</option>
                            @foreach(\App\Components\Utils::admin_role_val as $key=>$value)
                                <option value="{{$key}}" {{$con_arr['role']==strval($key)?'selected':''}}>{{$value}}</option>
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
                 <a href="javascript:;" onclick="edit('添加管理员','{{URL::asset('admin/admin/edit')}}')"
                    class="btn btn-primary radius">
                     <i class="Hui-iconfont">&#xe600;</i>添加管理员
                 </a>
            </span>
            <span class="r">共有数据：<strong>{{$datas->total()}}</strong> 条</span>
        </div>
        <table class="table table-border table-bordered table-bg table-sort mt-10">
            <thead>
            <tr>
                <th scope="col" colspan="8">内部管理员列表</th>
            </tr>
            <tr class="text-c">
                {{--<th width="25"><input type="checkbox" name="" value=""></th>--}}
                <th width="40">ID</th>
                <th width="50">头像</th>
                <th width="100">登录名</th>
                <th width="120">手机</th>
                <th width="50">角色</th>
                <th width="130">加入时间</th>
                <th width="50">状态</th>
                <th width="60">操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($datas as $data)
                <tr class="text-c">
                    {{--<td><input type="checkbox" value="1" name=""></td>--}}
                    <td>{{$data->id}}</td>
                    <td>
                        <img src="{{ $data->avatar ? $data->avatar.'?imageView2/1/w/200/h/200/interlace/1/q/75|imageslim' : URL::asset('/img/default_headicon.png')}}"
                             class="img-rect-30 radius-5">
                    </td>
                    <td>{{$data->name}}</td>
                    <td>{{$data->phonenum}}</td>
                    <td>
                        {{$data->role_str}}
                    </td>
                    <td>{{$data->created_at?$data->created_at:'--'}}</td>
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
                           onclick="edit('添加管理员','{{URL::asset('admin/admin/edit')}}?id={{$data->id}})',{{$data->id}})"
                           class="ml-5 c-primary" style="text-decoration:none">
                            编辑
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="mt-20">
            {{ $datas->appends($con_arr)->links() }}
        </div>
    </div>

@endsection

@section('script')
    <script type="text/javascript">


        /*管理员-编辑*/
        function edit(title, url, id) {
            consoledebug.log("show_optRecord url:" + url);
            var index = layer.open({
                type: 2,
                area: ['850px', '550px'],
                fixed: false,
                maxmin: true,
                title: title,
                content: url
            });
        }

        /*管理员-停用*/
        function stop(obj, id) {
            consoledebug.log("stop id:" + id);
            layer.confirm('确认要停用吗？', function (index) {
                //此处请求后台程序，下方是成功后的前台处理
                var param = {
                    id: id,
                    status: 0,
                    _token: "{{ csrf_token() }}"
                }
                //从后台设置管理员状态
                admin_setStatus('{{URL::asset('')}}', param, function (ret) {
                    if (ret.status == true) {

                    }
                })
                $(obj).parents("tr").find(".td-manage").prepend('<a onClick="start(this,' + id + ')" href="javascript:;" title="启用" class="c-primary" style="text-decoration:none">启用</a>');
                $(obj).parents("tr").find(".td-status").html('<span class="label label-default radius">冻结</span>');
                $(obj).remove();
                layer.msg('已停用', {icon: 5, time: 1000});
            });
        }

        /*管理员-启用*/
        function start(obj, id) {
            layer.confirm('确认要启用吗？', function (index) {
                //此处请求后台程序，下方是成功后的前台处理
                var param = {
                    id: id,
                    status: 1,
                    _token: "{{ csrf_token() }}"
                }
                //从后台设置管理员状态
                admin_setStatus('{{URL::asset('')}}', param, function (ret) {
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