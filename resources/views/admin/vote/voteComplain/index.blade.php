@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 举报管理 <span
                class="c-gray en">&gt;</span> 举报列表 <a class="btn btn-success radius r btn-refresh"
                                                      style="line-height:1.6em;margin-top:3px"
                                                      title="刷新"
                                                      onclick="location.replace('{{URL::asset('/admin/vote/voteComplain/index')}}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>
    <div class="page-container">

        <div class="text-c">
            <form id="search_form" action="{{URL::asset('admin/vote/voteComplain/index')}}" method="post" class="form-horizontal">
                {{csrf_field()}}
                <div class="Huiform text-r">
                    <span class="select-box" style="width:150px">
                        <select class="select" name="status" id="status" size="1">
                            <option value="" {{$con_arr['status']==""?'selected':''}}>全部状态</option>
                            @foreach(\App\Components\Utils::VOTE_COMPLAIN_STATUS_VAL as $key=>$value)
                                <option value="{{$key}}" {{$con_arr['status']==strval($key)?'selected':''}}>{{$value}}</option>
                            @endforeach
                        </select>
                    </span>
                    <button type="submit" class="btn btn-success" id="" name="">
                        <i class="Hui-iconfont">&#xe665;</i> 搜索
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-20">
            <table class="table table-border table-bordered table-bg table-sort">
                <thead>
                <tr>
                    <th scope="col" colspan="7">举报列表<span
                                class="r">共有数据：<strong>{{$datas->total()}}</strong> 条</span></th>
                </tr>
                <tr class="text-c">
                    {{--<th width="25"><input type="checkbox" name="" value=""></th>--}}
                    <th width="40">ID</th>
                    <th width="150">举报人</th>
                    <th width="120">大赛/选手信息</th>
                    <th width="200">举报内容</th>
                    <th width="40">状态</th>
                    <th width="40">投诉时间</th>
                    <th width="50">操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($datas as $data)
                    <tr class="text-c">
                        {{--<td><input type="checkbox" value="1" name=""></td>--}}
                        <td>{{$data->id}}</td>
                        <td>
                            <div>
                                <span>用户信息</span>
                                <a href="javascript:;"
                                   class="" style="text-decoration:none"
                                   onclick="creatIframe('{{URL::asset('/admin/user/info')}}?id={{$data->user->id}}','用户-{{$data->user->nick_name}}')">
                                    <span class="ml-5 c-primary">{{$data->user?$data->user->nick_name:'--'}}</span>
                                </a>
                            </div>
                            <div class="mt-5">
                                <span>称呼</span><span class="ml-5">{{$data->name}}</span>
                            </div>
                            <div class="mt-5">
                                <span>电话</span><span class="ml-5">{{$data->phonenum}}</span>
                            </div>
                        </td>
                        <td>
                            @if($data->activity)
                                <div class="">
                                    <span>关联大赛</span>
                                    <a href="javascript:;"
                                       class="" style="text-decoration:none"
                                       onclick="creatIframe('{{URL::asset('/admin/vote/voteActivity/edit')}}?id={{$data->activity->id}}','大赛信息-{{$data->activity->name}}')">
                                        <span class="ml-5 c-primary">{{$data->activity?$data->activity->name:'--'}}</span>
                                    </a>
                                </div>
                                <div class="mt-5">
                                    <span>第一责任人</span><span
                                            class="ml-5">{{$data->activity->c_admin1?$data->activity->c_admin1->name:'--'}}</span>
                                </div>
                                <div class="mt-5">
                                    <span>第二责任人</span><span
                                            class="ml-5">{{$data->activity->c_admin2?$data->activity->c_admin2->name:'--'}}</span>
                                </div>
                                <div class="mt-5">
                                    <span>地推团队</span><span
                                            class="ml-5">{{$data->activity->vote_team?$data->activity->vote_team->name:'--'}}</span>
                                </div>
                            @endif
                            <div class="mt-5">
                                <span>关联选手</span><span
                                        class="ml-5">{{$data->vote_user?$data->vote_user->name:'--'}}</span>
                            </div>
                        </td>
                        <td>
                            {{$data->content}}
                        </td>
                        <td class="td-status">
                            @if($data->status=="1")
                                <span class="label label-success radius">已解决</span>
                            @else
                                <span class="label label-default radius">未解决</span>
                            @endif
                        </td>
                        <td>{{$data->created_at}}</td>
                        <td class="td-manage">
                            @if($data->status=="1")
                                <a style="text-decoration:none" onClick="stop(this,'{{$data->id}}')"
                                   href="javascript:;" class="c-primary"
                                   title="未解决">
                                    未解决
                                </a>
                            @else
                                <a style="text-decoration:none" onClick="start(this,'{{$data->id}}')"
                                   href="javascript:;" class="c-primary"
                                   title="解决">
                                    已解决
                                </a>
                            @endif
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


        /*举报-未解决*/
        function stop(obj, id) {
            consoledebug.log("stop id:" + id);
            layer.confirm('确认要未解决吗？', function (index) {
                //此处请求后台程序，下方是成功后的前台处理
                var param = {
                    id: id,
                    status: 0,
                    _token: "{{ csrf_token() }}"
                }
                //从后台设置举报状态
                vote_voteComplain_setStatus('{{URL::asset('')}}', param, function (ret) {
                    if (ret.status == true) {

                    }
                })
                $(obj).parents("tr").find(".td-manage").prepend('<a onClick="start(this,' + id + ')" href="javascript:;" title="解决" class="c-primary" style="text-decoration:none">解决</a>');
                $(obj).parents("tr").find(".td-status").html('<span class="label label-default radius">未解决</span>');
                $(obj).remove();
                layer.msg('已未解决', {icon: 5, time: 1000});
            });
        }

        /*举报-解决*/
        function start(obj, id) {
            layer.confirm('确认要解决吗？', function (index) {
                //此处请求后台程序，下方是成功后的前台处理
                var param = {
                    id: id,
                    status: 1,
                    _token: "{{ csrf_token() }}"
                }
                //从后台设置举报状态
                vote_voteComplain_setStatus('{{URL::asset('')}}', param, function (ret) {
                    if (ret.status == true) {

                    }
                })
                $(obj).parents("tr").find(".td-manage").prepend('<a onClick="stop(this,' + id + ')" href="javascript:;" title="未解决" class="c-primary" style="text-decoration:none">未解决</a>');
                $(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">已解决</span>');
                $(obj).remove();
                layer.msg('已解决', {icon: 6, time: 1000});
            });
        }


    </script>
@endsection