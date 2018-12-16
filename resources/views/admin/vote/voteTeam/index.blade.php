@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 地推团队管理 <span
                class="c-gray en">&gt;</span> 地推团队列表 <a class="btn btn-success radius r btn-refresh"
                                                        style="line-height:1.6em;margin-top:3px"
                                                        title="刷新"
                                                        onclick="location.replace('{{URL::asset('/admin/vote/voteTeam/index')}}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>
    <div class="page-container">

        <div class="text-c">
            <form id="search_form" action="{{URL::asset('/admin/vote/voteTeam/index')}}" method="post"
                  class="form-horizontal">
                {{csrf_field()}}
                <div class="Huiform text-r">
                    <input id="search_word" name="search_word" type="text" class="input-text" style="width:350px"
                           placeholder="根据地推团队名称、联系人名、联系电话进行检索" value="{{$con_arr['search_word']}}">
                    <input id="id" name="id" type="text" class="input-text" style="width:100px"
                           placeholder="地推团队id" value="{{$con_arr['id']}}">
                    <button type="submit" class="btn btn-success" id="" name="">
                        <i class="Hui-iconfont">&#xe665;</i> 搜索
                    </button>
                </div>
            </form>
        </div>

        <div class="cl pd-5 bg-1 bk-gray mt-20">
            <span class="l">
                 <a href="javascript:;"
                    onclick="edit('添加地推团队','{{URL::asset('/admin/vote/voteTeam/edit')}}')"
                    class="btn btn-primary radius">
                     <i class="Hui-iconfont">&#xe600;</i> 添加地推团队
                 </a>
            </span>
            <span class="r">共有数据：<strong>{{$datas->total()}}</strong> 条</span>
        </div>

        <div class="mt-20">
            <table class="table table-border table-bordered table-bg table-sort">
                <thead>
                <tr>
                    <th scope="col" colspan="12">地推团队列表<span
                                class="r">共有数据：<strong>{{$datas->total()}}</strong> 条</span></th>
                </tr>
                <tr class="text-c">
                    {{--<th width="25"><input type="checkbox" name="" value=""></th>--}}
                    <th width="20">ID</th>
                    <th width="100">团队名称</th>
                    <th width="50">省份</th>
                    <th width="50">城市</th>
                    <th width="60">联系人</th>
                    <th width="80">电话</th>
                    <th width="80">邮箱</th>
                    <th width="50">创建人员</th>
                    <th width="100">创建时间</th>
                    <th width="100">可见金额</th>
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
                            {{$data->name?$data->name:'--'}}
                        </td>
                        <td>
                            {{$data->province?$data->province:'--'}}
                        </td>
                        <td>
                            {{$data->city?$data->city:'--'}}
                        </td>
                        <td>
                            {{$data->contact?$data->contact:'--'}}
                        </td>
                        <td>
                            {{$data->phonenum?$data->phonenum:'--'}}
                        </td>
                        <td>
                            {{$data->email?$data->email:'--'}}
                        </td>
                        <td>
                            {{$data->admin->name}}
                        </td>
                        <td>{{$data->created_at}}</td>
                        <td>
                            {{$data->amount?$data->amount:'--'}}
                        </td>
                        <td class="td-status">
                            @if($data->status=="1")
                                <span class="label label-success radius">正常</span>
                            @else
                                <span class="label label-default radius">冻结</span>
                            @endif
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
                                   onclick="edit('编辑地推团队','{{URL::asset('/admin/vote/voteTeam/edit')}}?id={{$data->id}})',{{$data->id}})"
                                   class="c-primary ml-5" style="text-decoration:none">
                                    编辑
                                </a>
                            </div>
                            <div class="mt-5">
                                <a title="编辑" href="javascript:;" class="c-primary"
                                   onclick="layer_show('查看二维码', '{{route('team.qrcode', ['id' => $data->id])}}', 400, 400)"
                                   style=" text-decoration:none">
                                    查看二维码
                                </a>
                            </div>
                            <div class="mt-5">
                                <a class="c-primary" style="text-decoration:none"
                                   onclick="creatIframe('{{URL::asset('/admin/vote/voteTeam/info')}}?id={{$data->id}}', '地推团队-{{$data->name}}');">
                                    收入详情
                                </a>
                            </div>
                            <div class="mt-5">
                                <a class="c-primary" style="text-decoration:none"
                                   onclick="creatIframe('{{URL::asset('/admin/vote/voteActivity/index')}}?vote_team_id={{$data->id}}', '地推团队-{{$data->name}}');">
                                    负责活动
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
        /*地推团队-增加*/
        function edit(title, url) {
            var index = layer.open({
                type: 2,
                title: title,
                content: url
            });
            layer.full(index);
        }

        /*地推团队-删除*/
        function del(obj, id) {
            layer.alert('不能删除团队，否则将导致数据混乱，请联系技术团队 TerryQi负责');
        }

        /*地推团队-停用*/
        function stop(obj, id) {
            consoledebug.log("stop id:" + id);
            layer.confirm('确认要停用吗？', function (index) {
                //此处请求后台程序，下方是成功后的前台处理
                var param = {
                    id: id,
                    status: 0,
                    _token: "{{ csrf_token() }}"
                }
                //从后台设置地推团队状态
                vote_voteTeam_setStatus('{{URL::asset('')}}', param, function (ret) {
                    if (ret.status == true) {

                    }
                })
                $(obj).parents("tr").find(".td-manage").prepend('<a onClick="start(this,' + id + ')" href="javascript:;" title="启用" class="c-primary" style="text-decoration:none">启用</a>');
                $(obj).parents("tr").find(".td-status").html('<span class="label label-default radius">冻结</span>');
                $(obj).remove();
                layer.msg('已停用', {icon: 5, time: 1000});
            });
        }

        /*地推团队-启用*/
        function start(obj, id) {
            layer.confirm('确认要启用吗？', function (index) {
                //此处请求后台程序，下方是成功后的前台处理
                var param = {
                    id: id,
                    status: 1,
                    _token: "{{ csrf_token() }}"
                }
                //从后台设置地推团队状态
                vote_voteTeam_setStatus('{{URL::asset('')}}', param, function (ret) {
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