@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 公众号素材管理 <span
                class="c-gray en">&gt;</span> 公众号素材列表 <a class="btn btn-success radius r btn-refresh"
                                                         style="line-height:1.6em;margin-top:3px"
                                                         title="刷新"
                                                         onclick="location.replace('{{URL::asset('/admin/gzh/material/index')}}?busi_name={{$con_arr['busi_name']}}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>
    <div class="page-container">
        <div class="text-c">
            <form id="search_form"  action="{{URL::asset('/admin/gzh/material/index')}}" method="post" class="form-horizontal">
                {{csrf_field()}}
                <div class="Huiform text-r">
                    <input id="busi_name" name="busi_name" type="text" class="input-text hidden" style="width:250px"
                           placeholder="公众号代码" value="{{$con_arr['busi_name']}}">
                    <span class="select-box" style="width:150px">
                        <select class="select" name="type" id="type" size="1">
                            <option value="" {{$con_arr['type']==""?'selected':''}}>全部类型</option>
                            @foreach(\App\Components\Utils::MATERIAL_TYPE_VAL as $key=>$value)
                                <option value="{{$key}}" {{$con_arr['type']==strval($key)?'selected':''}}>{{$value}}</option>
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
                    onclick="edit('添加素材','{{URL::asset('/admin/gzh/material/edit')}}?busi_name={{$con_arr['busi_name']}}')"
                    class="btn btn-primary radius">
                     <i class="Hui-iconfont">&#xe600;</i> 添加素材
                 </a>
            </span>
            {{--<span class="r">共有数据：<strong>{{$datas->count()}}</strong> 条</span>--}}
        </div>

        <div class="mt-20">
            <table class="table table-border table-bordered table-bg table-sort">
                <thead>
                <tr>
                    <th scope="col" colspan="8">公众号素材列表：此处素材为永久素材，公众号最多可以有5000个永久素材，用于配置自动回复、菜单、消息等</th>
                </tr>
                <tr class="text-c">
                    {{--<th width="25"><input type="checkbox" name="" value=""></th>--}}
                    <th width="40">ID</th>
                    <th width="40">素材类型</th>
                    <th width="150">MEDIA_ID</th>
                    <th width="100">素材信息</th>
                    <th width="100">备注</th>
                    <th width="50">创建人员</th>
                    <th width="100">创建时间</th>
                    <th width="50">操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($datas as $data)
                    <tr class="text-c">
                        {{--<td><input type="checkbox" value="1" name=""></td>--}}
                        <td>{{$data->id}}</td>
                        <td>
                            <span class="label label-success radius">{{$data->type_str}}</span>
                        </td>
                        <td>
                            {{$data->media_id}}
                        </td>
                        <td>
                            @if(isset($data->url))
                                <div>链接：{{$data->url}}</div>
                            @endif
                            @if(isset($data->title))
                                <div>标题：{{$data->title}}</div>
                            @endif
                            @if(isset($data->description))
                                <div>描述：{{$data->description}}</div>
                            @endif
                        </td>
                        <td>
                            {{ isset($data->remark)?$data->remark:'--'}}
                        </td>
                        <td>
                            {{$data->admin->name}}
                        </td>
                        <td>{{$data->created_at}}</td>
                        <td class="td-manage">
                            <a title="删除" href="javascript:;"
                               onclick="del(this,'{{$data->id}}','{{$con_arr['busi_name']}}')"
                               class="ml-5 c-primary"
                               style="text-decoration:none">
                                删除
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
        /*公众号素材-增加*/
        function edit(title, url) {
            var index = layer.open({
                type: 2,
                title: title,
                content: url
            });
            layer.full(index);
        }

        /*公众号素材-删除*/
        function del(obj, id, busi_name) {
            layer.confirm('确认要删除吗？', function (index) {
                //进行后台删除
                var param = {
                    id: id,
                    busi_name: busi_name,
                    _token: "{{ csrf_token() }}"
                }
                material_del('{{URL::asset('')}}', param, function (ret) {
                    if (ret.result == true) {
                        $(obj).parents("tr").remove();
                        layer.msg('已删除', {icon: 1, time: 1000});
                        $("#search_form").submit();
                    } else {
                        layer.msg('删除失败', {icon: 2, time: 1000})
                    }
                })
            });
        }


    </script>
@endsection