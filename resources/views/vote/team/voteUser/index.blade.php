@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 参赛选手管理 <span
                class="c-gray en">&gt;</span> 参赛选手列表 <a class="btn btn-success radius r btn-refresh"
                                                        style="line-height:1.6em;margin-top:3px"
                                                        title="刷新"
                                                        onclick="location.replace('{{route('team.voteUser.index', $con_arr)}}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>
    <div class="page-container">

        <div class="text-c">
            <form id="search_form" action="{{route('team.voteUser.index')}}" method="post"
                  class="form-horizontal">
                {{csrf_field()}}
                <div class="Huiform text-r">
                    <span>大赛id</span>
                    <input id="activity_id" name="activity_id" type="text" class="input-text" style="width:100px"
                           placeholder="大赛id" value="{{$con_arr['activity_id']}}">
                    <span class="ml-10">选手id</span>
                    <input id="vote_user_id" name="vote_user_id" type="text" class="input-text" style="width:100px"
                           placeholder="选手id" value="{{$con_arr['vote_user_id']}}">
                    <input id="search_word" name="search_word" type="text" class="input-text ml-5" style="width:350px"
                           placeholder="根据选手姓名、编号或手机号进行检索" value="{{$con_arr['search_word']}}">

                    <span class="select-box" style="width:100px">
                        <select class="select" name="audit_status" id="audit_status" size="1">
                            <option value="" {{$con_arr['audit_status']==""?'selected':''}}>全部状态</option>
                            @foreach(\App\Components\Utils::VOTE_USER_AUDIT_STATUS_VAL as $key=>$value)
                                <option value="{{$key}}" {{$con_arr['audit_status']==strval($key)?'selected':''}}>{{$value}}</option>
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
                    <th scope="col" colspan="7">选手列表<span
                                class="r">共有数据：<strong>{{$datas->total()}}</strong> 条</span></th>
                </tr>
                <tr class="text-c">
                    {{--<th width="25"><input type="checkbox" name="" value=""></th>--}}
                    <th width="150">报名信息</th>
                    <th width="200">作品信息</th>
                    <th width="100">数据</th>
                    <th width="50">状态</th>
                    <th width="80">操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($datas as $data)
                    <tr class="text-c">
                        <td>
                            <div>
                                <a class="c-primary" style="text-decoration:none"
                                   onclick="creatIframe('{{route('team.activity.edit')}}?id={{$data->activity->id}})', '大赛信息-{{$data->activity->name}}');">{{$data->activity->name}}</a>
                            </div>
                            <div class="mt-5">
                                @if($data->user)
                                    关联用户：<a class="c-primary" style="text-decoration:none"
                                            onclick="creatIframe('{{URL::asset('/admin/user/info')}}?id={{$data->user->id}})', '用户信息-{{$data->user->nick_name}}');">{{$data->user->nick_name}}
                                        ({{$data->user->id}})</a>
                                @else
                                    关联用户：系统录入用户
                                @endif
                            </div>
                            <div class="mt-5">
                                录入管理员：<span class="">{{$data->admin?$data->admin->name:'--'}}</span>
                            </div>
                            <div class="mt-5">
                                大赛编号：<span class="">{{$data->activity->code}}-{{$data->code}}</span>
                            </div>
                            <div class="mt-5">
                                报名姓名：<span class="">{{$data->name}}（{{$data->id}}）</span>
                            </div>
                            <div class="mt-5">
                                报名类型：<span class="">{{$data->type_str}}</span>
                            </div>
                            <div class="mt-5">
                                手机号码：<span class="">{{$data->phonenum?$data->phonenum:"--"}}</span>
                            </div>
                            <div class="mt-5">
                                审核状态：
                                @if($data->audit_status=='0')
                                    <span class="label label-default">{{$data->audit_status_str}}</span>
                                @endif
                                @if($data->audit_status=='1')
                                    <span class="label label-primary">{{$data->audit_status_str}}</span>
                                @endif
                                @if($data->audit_status=='2')
                                    <span class="label label-danger">{{$data->audit_status_str}}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="mt-5">
                                <span>作品名称：{{$data->work_name?$data->work_name:'--'}}</span>
                            </div>
                            <div class="mt-5">
                                <span>作品描述：{{$data->work_desc?$data->work_desc:'--'}}</span>
                            </div>
                            <div class="row">
                                @foreach($data->img_arr as $img)
                                    <div class="col-md-4">
                                        <img src="{{$img}}{{strpos($img,'?')==false?'?imageView2/2/w/300/interlace/1/q/75':'/w/300'}}"
                                             style="width: 90px;" class="pd-10">
                                    </div>
                                @endforeach
                            </div>
                        </td>
                        <td>
                            <div>
                                <span>票数</span><span class="ml-5 label label-primary">{{$data->vote_num}}</span>
                            </div>
                            <div class="mt-5">
                                <span>礼品总额</span><span class="ml-5 label label-primary">{{$data->gift_money}}</span>
                            </div>
                            <div class="mt-5">
                                <span>显示次数</span><span class="ml-5 label label-primary">{{$data->show_num}}</span>
                            </div>
                            <div class="mt-5">
                                <span>分享次数</span><span class="ml-5 label label-primary">{{$data->share_num}}</span>
                            </div>
                            <div class="mt-5">
                                <span>粉丝数</span><span class="ml-5 label label-primary">{{$data->fans_num}}</span>
                            </div>
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
                                   onclick="edit('编辑参赛选手-{{$data->name}}','{{route('team.activity.editVoteUser')}}?id={{$data->id}}',{{$data->id}})"
                                   class="c-primary ml-5" style="text-decoration:none">
                                    编辑
                                </a>
                            </div>
                            <div class="mt-5">
                                <a style="text-decoration:none" onClick="audit(this,'{{$data->id}}','1')"
                                   href="javascript:;" class="c-primary"
                                   title="审核通过">
                                    审核通过
                                </a>
                            </div>
                            <div class="mt-5">
                                <a style="text-decoration:none" onClick="audit(this,'{{$data->id}}','2')"
                                   href="javascript:;" class="c-primary"
                                   title="审核驳回">
                                    审核驳回
                                </a>
                            </div>
                            <div class="mt-5">
                                <a class="c-primary" style="text-decoration:none"
                                   onclick="creatIframe('{{route('team.activity.voteUserInfo')}}?id={{$data->id}})', '选手信息-{{$data->name}}');">
                                    详细信息
                                </a>
                            </div>

                            <p>
                                {!! QrCode::size(100)->generate(env('SYATC_CN_URL') . 'vote/person?vote_user_id=' . $data->id) !!}
                            </p>
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
        /*参赛选手-增加*/
        function edit(title, url) {
            var index = layer.open({
                type: 2,
                title: title,
                content: url
            });
            layer.full(index);
        }

        /*参赛选手-删除*/
        function del(obj, id) {
            layer.alert('不能删除参赛选手，否则将导致数据混乱，请联系技术团队 TerryQi负责');
        }

        /*参赛选手-停用*/
        function stop(obj, id) {
            consoledebug.log("stop id:" + id);
            layer.confirm('确认要停用吗？', function (index) {
                //此处请求后台程序，下方是成功后的前台处理
                var param = {
                    id: id,
                    status: 0,
                    _token: "{{ csrf_token() }}"
                }
                //从后台设置参赛选手状态
                vote_team_voteUser_setStatus('{{route('team.activity.setVoteUserStatus')}}', param, function (ret) {
                    if (ret.status == true) {

                    }
                })
                $(obj).parents("tr").find(".td-manage").prepend('<a onClick="start(this,' + id + ')" href="javascript:;" title="启用" class="c-primary" style="text-decoration:none">启用</a>');
                $(obj).parents("tr").find(".td-status").html('<span class="label label-default radius">冻结</span>');
                $(obj).remove();
                layer.msg('已停用', {icon: 5, time: 1000});
            });
        }

        /*参赛选手-启用*/
        function start(obj, id) {
            layer.confirm('确认要启用吗？', function (index) {
                //此处请求后台程序，下方是成功后的前台处理
                var param = {
                    id: id,
                    status: 1,
                    _token: "{{ csrf_token() }}"
                }
                //从后台设置参赛选手状态
                vote_team_voteUser_setStatus('{{route('team.activity.setVoteUserStatus')}}', param, function (ret) {
                    if (ret.status == true) {

                    }
                })
                $(obj).parents("tr").find(".td-manage").prepend('<a onClick="stop(this,' + id + ')" href="javascript:;" title="停用" class="c-primary" style="text-decoration:none">停用</a>');
                $(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">正常</span>');
                $(obj).remove();
                layer.msg('正常', {icon: 6, time: 1000});
            });
        }

        /*参赛选手-审核状态*/
        function audit(obj, id, audit_status) {
            consoledebug.log("audit id:" + id + " audit_status:" + audit_status);
            layer.confirm('确认要进行审核吗？', function (index) {
                //此处请求后台程序，下方是成功后的前台处理
                var param = {
                    id: id,
                    audit_status: audit_status,
                    _token: "{{ csrf_token() }}"
                }
                //从后台设置参赛选手状态
                vote_team_voteUser_setAuditStatus('{{route('team.activity.setVoteUserAuditStatus')}}', param, function (ret) {
                    layer.msg('完成审核', {icon: 1, time: 1000});
                    $('.btn-refresh').click();
                })
            });
        }


    </script>
@endsection