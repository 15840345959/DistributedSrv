@extends('admin.layouts.app')

@section('content')

    <style>
        p {
            margin: 0;
        }
    </style>

    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 大赛管理 <span
                class="c-gray en">&gt;</span> 大赛列表 <a class="btn btn-success radius r btn-refresh"
                                                      style="line-height:1.6em;margin-top:3px"
                                                      title="刷新"
                                                      onclick="location.replace('{{URL::asset('/admin/vote/voteActivity/index')}}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>
    <div class="page-container">

        <div class="text-c">
            <form id="search_form" action="{{URL::asset('/admin/vote/voteActivity/index')}}" method="post"
                  class="form-horizontal">
                {{csrf_field()}}
                <div class="Huiform text-r">
                    <input id="search_word" name="search_word" type="text" class="input-text" style="width:250px"
                           placeholder="根据名称或关键词称进行检索" value="{{$con_arr['search_word']}}">
                    <span class="ml-5">大赛id：</span>
                    <input id="id" name="id" type="text" class="input-text" style="width:100px"
                           placeholder="大赛id" value="{{$con_arr['id']}}">
                    <span class="ml-5">第一负责人id：</span>
                    <input id="c_admin_id1" name="c_admin_id1" type="text" class="input-text" style="width:100px"
                           placeholder="第一负责人id" value="{{$con_arr['c_admin_id1']}}">
                    <span class="ml-5">地推团队id：</span>
                    <span class="select-box" style="width: 200px;">
                        <select id="vote_team_id" name="vote_team_id" class="select">
                            <option value="">请选择</option>
                            @foreach($vote_teams as $vote_team)
                                <option value="{{$vote_team->id}}" {{$con_arr['vote_team_id']==$vote_team->id?'selected':''}}>{{$vote_team->name}}</option>
                            @endforeach
                        </select>
                    </span>
                </div>
                <div class="Huiform text-r mt-10">
                    <span class="ml-5">报名状态：</span>
                    <span class="select-box" style="width: 150px;">
                        <select id="apply_status" name="apply_status" class="select">
                            <option value="">请选择</option>
                            @foreach(\App\Components\Utils::VOTE_APPLY_STATUS_VAL as $key=>$value)
                                <option value="{{$key}}" {{$con_arr['apply_status']==strval($key)?'selected':''}}>{{$value}}</option>
                            @endforeach
                        </select>
                    </span>
                    <span class="ml-5">投票状态：</span>
                    <span class="select-box" style="width: 100px;">
                        <select id="vote_status" name="vote_status" class="select">
                            <option value="">请选择</option>
                            @foreach(\App\Components\Utils::VOTE_VOTE_STATUS_VAL as $key=>$value)
                                <option value="{{$key}}" {{$con_arr['vote_status']==strval($key)?'selected':''}}>{{$value}}</option>
                            @endforeach
                        </select>
                    </span>
                    <span class="ml-5">激活状态：</span>
                    <span class="select-box" style="width: 100px;">
                        <select id="valid_status" name="valid_status" class="select">
                            <option value="">请选择</option>
                            @foreach(\App\Components\Utils::VOTE_ACTIVITY_VALID_STATUS_VAL as $key=>$value)
                                <option value="{{$key}}" {{$con_arr['valid_status']==strval($key)?'selected':''}}>{{$value}}</option>
                            @endforeach
                        </select>
                    </span>
                    <span class="ml-5">冻结状态：</span>
                    <span class="select-box" style="width: 100px;">
                        <select id="status" name="status" class="select">
                            <option value="">请选择</option>
                            @foreach(\App\Components\Utils::VOTE_STATUS_VAL as $key=>$value)
                                <option value="{{$key}}" {{$con_arr['status']==strval($key)?'selected':''}}>{{$value}}</option>
                            @endforeach
                        </select>
                    </span>
                    <span class="ml-5">结束日期：</span>
                    <input id="vote_end_at" name="vote_end_at" type="date" class="input-text"
                           style="width: 150px;"
                           value="{{ isset($con_arr['vote_end_at']) ? $con_arr['vote_end_at'] : '' }}"
                           placeholder="请输入结束日期">
                    <button type="submit" class="btn btn-success" id="" name="">
                        <i class="Hui-iconfont">&#xe665;</i> 搜索
                    </button>
                </div>
            </form>
        </div>

        <div class="cl pd-5 bg-1 bk-gray mt-20">
            <span class="l">
                 <a href="javascript:;"
                    onclick="edit('新建大赛','{{URL::asset('/admin/vote/voteActivity/edit')}}')"
                    class="btn btn-primary radius">
                     <i class="Hui-iconfont">&#xe600;</i> 添加大赛
                 </a>
            </span>
            <span class="r">共有数据：<strong>{{$datas->total()}}</strong> 条</span>
        </div>

        <div class="mt-20">
            <table class="table table-border table-bordered table-bg table-sort">
                <thead>
                <tr>
                    <th scope="col" colspan="7">大赛列表</th>
                </tr>
                <tr class="text-c">
                    {{--<th width="25"><input type="checkbox" name="" value=""></th>--}}
                    <th width="100">活动名称</th>
                    <th width="280">基础信息</th>
                    <th width="100">数据</th>
                    <th width="100">状态</th>
                    <th width="80">操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($datas as $data)
                    <tr class="text-c">
                        <td>
                            <div>
                                <img src="{{$data->img}}" style="width: 80px;">
                            </div>
                            <div class="mt-5">
                                <span>{{$data->name}}</span>
                            </div>
                            <div>
                                <span>编号：{{$data->id}}</span>
                                <span class="ml-5 c-primary">关键词：{{$data->code?$data->code:'--'}}</span>
                            </div>
                        </td>
                        <td>
                            <div>
                                <span>报名时间：</span><span class="ml-5">{{$data->apply_start_time}}
                                    -{{$data->apply_end_time}}</span>
                            </div>
                            <div class="mt-5">
                                <span>投票时间：</span><span class="ml-5">{{$data->vote_start_time}}
                                    -{{$data->vote_end_time}}</span>
                            </div>
                            <div class="mt-5">
                                <span>报名状态：</span><span
                                        class="ml-5 c-primary label label-primary">{{$data->apply_status_str}}</span>
                            </div>
                            <div class="mt-5">
                                <span class="">投票状态：</span><span
                                        class="ml-5 label label-secondary">{{$data->vote_status_str}}</span>
                            </div>
                            <div class="mt-5">
                                <span class="">激活状态：</span><span
                                        class="ml-5 label label-success">{{$data->valid_status_str}}</span>
                            </div>
                            <div class="mt-5">
                                <span class="">结算状态：</span><span
                                        class="ml-5 label label-warning">{{$data->is_settle_str}}</span>
                            </div>
                            <div class="mt-5">
                                <span>一等奖：</span><span class="ml-5 c-primary">{{$data->first_prize_num}}个</span>
                                <span>二等奖：</span><span class="ml-5 c-primary">{{$data->second_prize_num}}个</span>
                                <span>三等奖：</span><span class="ml-5 c-primary">{{$data->third_prize_num}}个</span>
                                {{--<span>优秀奖：</span><span class="ml-5 c-primary">{{$data->honor_prize_num}}个</span>--}}
                            </div>
                            <div class="mt-5">
                                <span class="">大赛链接：</span><span
                                        class="ml-5 c-primary">
                                    {{env('SYATC_CN_URL')}}vote/index?activity_id={{$data->id}}
                                </span>
                            </div>
                        </td>
                        <td>
                            <div>
                                <span>参与人数</span><span class="ml-5 label label-secondary">{{$data->join_num}}</span>
                            </div>
                            <div class="mt-5">
                                <span>投票总数</span><span class="ml-5 label label-primary">{{$data->vote_num}}</span>
                            </div>
                            <div class="mt-5">
                                <span>展示总量</span><span class="ml-5 label label-success">{{$data->show_num}}</span>
                            </div>
                            <div class="mt-5">
                                <span>礼物总数</span><span class="ml-5 label label-warning">{{$data->gift_num}}</span>
                            </div>
                            <div class="mt-5">
                                <span>礼物总额</span><span class="ml-5 label label-danger">{{$data->gift_money}}</span>
                            </div>
                            <div class="mt-5">
                                <span>分享总数</span><span class="ml-5 label label-primary">{{$data->share_num}}</span>
                            </div>
                            <div class="mt-5">
                                <span>投诉总数</span><span class="ml-5 label label-secondary">{{$data->complain_num}}</span>
                            </div>
                        </td>
                        <td class="td-status">
                            <div>
                                @if($data->status=="1")
                                    <span class="label label-success radius">正常</span>
                                @else
                                    <span class="label label-default radius">冻结</span>
                                @endif
                            </div>
                            <div class="mt-5">
                                <span>负责人1：{{$data->c_admin1?$data->c_admin1->name:'--'}}</span>
                            </div>
                            <div class="mt-5">
                                <span>负责人2：{{$data->c_admin2?$data->c_admin2->name:'--'}}</span>
                            </div>
                            <div class="mt-5">
                                <span>地推团队：{{$data->vote_team?$data->vote_team->name:'--'}}</span>
                            </div>
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
                                   onclick="edit('编辑大赛-{{$data->name}}','{{URL::asset('/admin/vote/voteActivity/edit')}}?id={{$data->id}}',{{$data->id}})"
                                   class="c-primary ml-5" style="text-decoration:none">
                                    编辑
                                </a>
                            </div>
                            <div class="mt-5">
                                <a style="text-decoration:none" onClick="copy('{{$data->id}}')"
                                   href="javascript:;" class="c-primary"
                                   title="复制大赛">
                                    复制大赛
                                </a>
                            </div>
                            <div class="mt-5">
                                <a title="地推结算" href="javascript:;"
                                   onclick="settle('地推结算', '{{route('voteActivity.settle', ['id' => $data->id])}}')"
                                   class="c-primary" style="text-decoration:none">
                                    地推结算
                                </a>
                            </div>
                            <div class="mt-5">
                                <a title="编辑" href="javascript:;" class="c-primary"
                                   onclick="layer_show('活动二维码', '{{route('voteActivity.qrcode', ['id' => $data->id])}}', 400, 400)"
                                   style=" text-decoration:none">
                                    活动二维码
                                </a>
                            </div>
                            <div class="mt-5">
                                <a title="综合统计" href="javascript:;"
                                   onclick="edit('综合统计-{{$data->name}}','{{URL::asset('/admin/vote/voteActivity/info')}}?id={{$data->id}}',{{$data->id}})"
                                   class="c-primary ml-5" style="text-decoration:none">
                                    综合统计
                                </a>
                            </div>
                            @if($data->vote_status == 2)
                                <div class="mt-5">
                                    <a title="获奖名单" href="javascript:;"
                                       onclick="exportPrizeStatements('{{$data->id}}')"
                                       class="c-primary" style="text-decoration:none">
                                        获奖名单
                                    </a>
                                </div>
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
         参数解释：
         title	标题
         url		请求的url
         id		需要操作的数据id
         w		弹出层宽度（缺省调默认值）
         h		弹出层高度（缺省调默认值）
         */
        /*大赛-增加*/
        function edit(title, url) {
            creatIframe(url, title)
        }

        /*大赛-删除*/
        function del(obj, id) {
            layer.alert('不能删除大赛，否则将导致数据混乱，请联系技术团队 TerryQi负责');
        }

        /*大赛-停用*/
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
                //从后台设置大赛状态
                vote_voteActivity_setStatus('{{URL::asset('')}}', param, function (ret) {
                    if (ret.result == true) {
                        layer.msg('已停用', {icon: 5, time: 1000});
                        $("#search_form").submit();
                    } else {
                        layer.msg(ret.message, {icon: 5, time: 2000});
                    }
                    layer.close(index);
                })

            });
        }

        /*大赛-启用*/
        function start(obj, id) {
            layer.confirm('确认要启用吗？', function (index) {
                //此处请求后台程序，下方是成功后的前台处理
                var param = {
                    id: id,
                    status: 1,
                    _token: "{{ csrf_token() }}"
                }

                var index = layer.load(2, {time: 10 * 1000}); //加载
                //从后台设置大赛状态
                vote_voteActivity_setStatus('{{URL::asset('')}}', param, function (ret) {
                    if (ret.result == true) {
                        layer.msg('正常', {icon: 6, time: 1000});
                        $("#search_form").submit();
                    } else {
                        layer.msg(ret.message, {icon: 5, time: 2000});
                    }
                    layer.close(index);
                })

            });
        }

        /*
         * 复制大赛
         *
         * By TerryQi
         *
         * 2018-07-24
         *
         */
        function copy(activity_id) {
            layer.confirm('确认要复制该大赛吗？', function (index) {
                //此处请求后台程序，下方是成功后的前台处理
                var param = {
                    id: activity_id,
                    _token: "{{ csrf_token() }}"
                }
                var index = layer.load(2, {time: 10 * 1000}); //加载
                //从后台设置大赛状态
                vote_voteActivity_copy('{{URL::asset('')}}', param, function (ret) {
                    if (ret.result == true) {
                        layer.msg('复制成功', {icon: 1, time: 1000});
                        //打开新的编辑窗口
                        creatIframe("{{URL::asset('/admin/vote/voteActivity/edit')}}?id=" + ret.ret.id + "", "编辑大赛-" + ret.ret.name);
                        $("#search_form").submit();
                    } else {
                        layer.msg(ret.message, {icon: 5, time: 2000});
                    }
                    layer.close(index);
                })
            });
        }

        /*
         * 进行结算
         *
         * By TerryQi
         *
         * 2018-09-19
         */
        function settle(title, url) {
            var index = layer.open({
                type: 2,
                title: title,
                content: url
            });
            layer.full(index);
        }

        function exportPrizeStatements(id) {
            console.log('id is : ', id)
            window.location.href = '{{route('voteActivity.prizeStatements')}}?id=' + id + '&_token=' + '{{csrf_token()}}'
        }

    </script>
@endsection