@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 菜单管理
        <span class="c-gray en">&gt;</span> 菜单列表 <a class="btn btn-success radius r btn-refresh"
                                                    style="line-height:1.6em;margin-top:3px"
                                                    href="javascript:location.replace(location.href);"
                                                    title="刷新"
                                                    onclick="location.replace('{{URL::asset('/admin/gzh/menu/index')}}?busi_name={{$con_arr['busi_name']}}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>
    {{--/{{$datas->type_id}}--}}
    <div class="page-container">

        <div class="cl pd-5 bg-1 bk-gray mt-20">
            <span class="l">
                 <a href="javascript:;"
                    onclick="edit('添加菜单项','{{URL::asset('/admin/gzh/menu/edit')}}?busi_name={{$con_arr['busi_name']}}')"
                    class="btn btn-primary radius">
                     <i class="Hui-iconfont">&#xe600;</i> 添加菜单项
                 </a>
            </span>
            <span class="l">
                 <a href="javascript:;" onclick="create_menu('{{$con_arr['busi_name']}}')"
                    class="btn btn-success radius ml-20">
                     <i class="Hui-iconfont">&#xe681;</i> 根据配置生成菜单
                 </a>
            </span>
            {{--<span class="r">共有数据：<strong>{{$datas->count()}}</strong> 条</span>--}}
        </div>
        <div class="mt-5">
            <span class="c-999 ml-10">编辑菜单后需要重新生成公众号菜单，这将有一定延迟，需要取消关注公众号、再次关注公众号才可以即刻查看效果</span>
        </div>
        <table class="table table-border table-bordered table-bg table-sort mt-20">
            <thead>
            <tr>
                <th scope="col" colspan="6">菜单列表</th>
            </tr>
            <tr class="text-c">
                <th width="50">ID</th>
                <th width="50">名称</th>
                <th width="50">父级菜单</th>
                <th width="50">类型</th>
                <th width="200">内容</th>
                <th width="50">操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($datas as $data)
                <tr class="text-c">
                    <td>{{$data->id}}</td>
                    <td>{{$data->name}}</td>
                    <td>
                        --
                    </td>
                    <td>
                        @if($data->level=='0'&&$data->f_id=='0')
                            {{$data->type_str?$data->type_str:'-'}}
                        @else
                            --
                        @endif
                    </td>
                    <td>
                        @if($data->level=='0'&&$data->f_id=='0')
                            @if($data->type=='view')
                                <div>链接：{{$data->url}}</div>
                            @endif
                            @if($data->type=='click')
                                <div>自定义事件：{{$data->key}}</div>
                            @endif
                            @if($data->type=='media_id')
                                <div>素材id：{{$data->media_id}}</div>
                            @endif
                            @if($data->type=='miniprogram')
                                <div>链接：{{$data->url}}</div>
                                <div>appid：{{$data->appid}}</div>
                                <div>pathpage：{{$data->pagepath}}</div>
                            @endif
                        @else
                            --
                        @endif
                    </td>
                    <td>
                        <a title="编辑" href="javascript:;"
                           onclick="edit('菜单编辑','{{URL::asset('/admin/gzh/menu/edit')}}?id={{$data->id}})&busi_name={{$con_arr['busi_name']}}',{{$data->id}})"
                           class="ml-5 c-primary" style="text-decoration:none">
                            编辑
                        </a>
                        <a title="删除" href="javascript:;" onclick="del(this,'{{$data->id}}')" class="ml-5 c-primary"
                           style="text-decoration:none">
                            删除
                        </a>
                    </td>
                </tr>
                @foreach($data->sub_menus as $sub_menu)
                    <tr class="text-c">
                        <td>{{$sub_menu->id}}</td>
                        <td>{{$sub_menu->name}}</td>
                        <td>{{$data->name}}</td>
                        <td>{{$sub_menu->type_str?$sub_menu->type_str:'-'}}</td>
                        <td>
                            @if($sub_menu->type=='view')
                                <div>链接：{{$sub_menu->url}}</div>
                            @endif
                            @if($sub_menu->type=='click')
                                <div>自定义事件：{{$sub_menu->key}}</div>
                            @endif
                            @if($sub_menu->type=='media_id')
                                <div>素材id：{{$sub_menu->media_id}}</div>
                            @endif
                            @if($sub_menu->type=='miniprogram')
                                <div>链接：{{$sub_menu->url}}</div>
                                <div>appid：{{$sub_menu->appid}}</div>
                                <div>pathpage：{{$sub_menu->pagepath}}</div>
                            @endif
                        </td>
                        <td>
                            <a title="编辑" href="javascript:;"
                               onclick="edit('菜单编辑','{{URL::asset('/admin/gzh/menu/edit')}}?id={{$sub_menu->id}})&busi_name={{$con_arr['busi_name']}}',{{$data->id}})"
                               class="ml-5 c-primary" style="text-decoration:none">
                                编辑
                            </a>
                            <a title="删除" href="javascript:;" onclick="del(this,'{{$sub_menu->id}}')"
                               class="ml-5 c-primary"
                               style="text-decoration:none">
                                删除
                            </a>
                        </td>
                    </tr>
                @endforeach
            @endforeach
            </tbody>
        </table>
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
        /*菜单分类-增加*/
        /*菜单-增加*/
        function edit(title, url) {
            console.log("url:" + url);
            var index = layer.open({
                type: 2,
                title: title,
                content: url
            });
            layer.full(index);
        }

        /*菜单-删除*/
        function del(obj, id, busi_name) {
            layer.confirm('确认要删除菜单配置吗？删除后需要重新生成菜单', function (index) {
                //进行后台删除
                var param = {
                    id: id,
                    _token: "{{ csrf_token() }}"
                }
                menu_del('{{URL::asset('')}}', param, function (ret) {
                    if (ret.result == true) {
                        $(obj).parents("tr").remove();
                        layer.msg('已删除', {icon: 1, time: 1000});
                    } else {
                        layer.msg('删除失败', {icon: 2, time: 1000})
                    }
                })
            });
        }


        /*
         * 生成菜单
         * 
         * By TerryQI
         */
        function create_menu(busi_name) {
            console.log("create_menu busi_name:" + busi_name);
            layer.confirm('确认根据现有配置生成菜单吗？', function (index) {
                //进行后台删除
                var param = {
                    busi_name: busi_name,
                    _token: "{{ csrf_token() }}"
                }

                var index = layer.load(2, {time: 10 * 1000}); //加载

                menu_create('{{URL::asset('')}}', param, function (ret) {
                    console.log("ret:" + JSON.stringify(ret));
                    if (ret.result == true) {
                        layer.msg('已经创建菜单，请重新关注公众号查看效果', {icon: 1, time: 1000});
                    } else {
                        layer.msg('创建失败', {icon: 2, time: 1000})
                    }

                    layer.close(index);
                })
            });
        }

    </script>
@endsection