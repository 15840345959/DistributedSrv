<div class="cl pd-5 bg-1 bk-gray mt-20">
    <span class="l">
        <a href="javascript:;"
           onclick="edit('添加参赛选手','{{route('team.activity.editVoteUser')}}?activity_id={{$data->id}}')"
           class="btn btn-primary radius">
            <i class="Hui-iconfont">&#xe600;</i> 添加参赛选手
        </a>
    </span>
    <span class="l ml-10">
        <a href="javascript:;"
           onclick="clickImportVoteUser('批量导入选手-图片','{{route('team.activity.importVoteUser')}}?activity_id={{$data->id}}',{{$data->id}})"
           class="btn btn-primary radius">
            <i class="Hui-iconfont">&#xe600;</i> 批量添加参赛选手-图片类
        </a>
    </span>
    <span class="l ml-10">
        <a href="javascript:;"
           onclick="clickImportVoteUser('批量导入选手-视频','{{route('team.activity.importVoteUserVideo')}}?activity_id={{$data->id}}',{{$data->id}})"
           class="btn btn-primary radius">
            <i class="Hui-iconfont">&#xe600;</i> 批量添加参赛选手-视频类
        </a>
    </span>
    {{--<span class="r">共有数据：<strong>{{$datas->count()}}</strong> 条</span>--}}
</div>

<div class="mt-20">
    <table class="table table-border table-bordered table-bg table-sort">
        <thead>
        <tr>
            <th scope="col" colspan="7">选手列表</th>
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
        @foreach($data->vote_users as $vote_user)
            <tr class="text-c">
                <td>
                    <div>
                        @if($vote_user->user)
                            关联用户：
                            <a class="c-primary" style="text-decoration:none"
                               onclick="creatIframe('{{URL::asset('/admin/user/info')}}?id={{$vote_user->user->id}})', '用户信息-{{$vote_user->user->nick_name}}');">{{$vote_user->user->nick_name}}
                                ({{$vote_user->user->id}})</a>
                        @else
                            关联用户：系统录入用户
                        @endif
                    </div>
                    <div class="mt-5">
                        大赛编号：<span class="">{{$data->code}}-{{$vote_user->code}}</span>
                    </div>
                    <div class="mt-5">
                        报名姓名：<span class="">{{$vote_user->name}}（{{$vote_user->id}}）</span>
                    </div>
                    <div class="mt-5">
                        手机号码：<span class="">{{$vote_user->phonenum?$vote_user->phonenum:"--"}}</span>
                    </div>
                    <div class="mt-5">
                        审核状态：
                        @if($vote_user->audit_status=='0')
                            <span class="label label-default">{{$vote_user->audit_status_str}}</span>
                        @endif
                        @if($vote_user->audit_status=='1')
                            <span class="label label-primary">{{$vote_user->audit_status_str}}</span>
                        @endif
                        @if($vote_user->audit_status=='2')
                            <span class="label label-danger">{{$vote_user->audit_status_str}}</span>
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
                        @foreach($vote_user->img_arr as $img)
                            <div class="col-md-4">
                                <img src="{{$img}}{{strpos($img,'?')==false?'?imageView2/2/w/300/interlace/1/q/75':'/w/300'}}"
                                     style="width: 90px;" class="pd-10">
                            </div>
                        @endforeach
                    </div>
                </td>
                <td>
                    <div>
                        <span>票数</span><span class="ml-5 label label-primary">{{$vote_user->vote_num}}</span>
                    </div>
                    <div class="mt-5">
                        <span>礼品总额</span><span class="ml-5 label label-primary">{{$vote_user->gift_money}}</span>
                    </div>
                    <div class="mt-5">
                        <span>显示次数</span><span class="ml-5 label label-primary">{{$vote_user->show_num}}</span>
                    </div>
                    <div class="mt-5">
                        <span>分享次数</span><span class="ml-5 label label-primary">{{$vote_user->share_num}}</span>
                    </div>
                    <div class="mt-5">
                        <span>粉丝数</span><span class="ml-5 label label-primary">{{$vote_user->fans_num}}</span>
                    </div>
                </td>
                <td class="td-status">
                    @if($vote_user->status=="1")
                        <span class="label label-success radius">正常</span>
                    @else
                        <span class="label label-default radius">冻结</span>
                    @endif
                </td>
                <td class="td-manage">
                    <div>
                        @if($data->status=="1")
                            <a style="text-decoration:none" onClick="stop(this,'{{$vote_user->id}}')"
                               href="javascript:;" class="c-primary"
                               title="停用">
                                停用
                            </a>
                        @else
                            <a style="text-decoration:none" onClick="start(this,'{{$vote_user->id}}')"
                               href="javascript:;" class="c-primary"
                               title="启用">
                                启用
                            </a>
                        @endif
                        <a title="编辑" href="javascript:;"
                           onclick="edit('编辑参赛选手-{{$vote_user->name}}','{{route('team.activity.editVoteUser')}}?id={{$vote_user->id}}',{{$vote_user->id}})"
                           class="c-primary ml-5" style="text-decoration:none">
                            编辑
                        </a>
                    </div>
                    <div class="mt-5">
                        <a style="text-decoration:none" onClick="audit(this,'{{$vote_user->id}}','1')"
                           href="javascript:;" class="c-primary"
                           title="审核通过">
                            审核通过
                        </a>
                    </div>
                    <div class="mt-5">
                        <a style="text-decoration:none" onClick="audit(this,'{{$vote_user->id}}','2')"
                           href="javascript:;" class="c-primary"
                           title="审核驳回">
                            审核驳回
                        </a>
                    </div>
                    <div class="mt-5">
                        <a class="c-primary" style="text-decoration:none"
                           onclick="creatIframe('{{route('team.activity.voteUserInfo')}}?id={{$vote_user->id}})', '选手信息-{{$vote_user->name}}');">
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
</div>


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

    //点击导入选手
    function clickImportVoteUser(title, url) {
        var index = layer.open({
            type: 2,
            title: title,
            content: url
        });
        layer.full(index);
    }

</script>
