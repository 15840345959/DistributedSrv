@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 管理日报 <span
                class="c-gray en">&gt;</span> 管理日报 <a class="btn btn-success radius r btn-refresh"
                                                      style="line-height:1.6em;margin-top:3px"
                                                      href="javascript:location.replace(location.href);" title="刷新"
                                                      onclick="location.replace('{{URL::asset('/admin/vote/voteStmt/daily')}}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>
    <div class="page-container">

        <div class="text-c">
            <form id="search_form" action="{{URL::asset('/admin/vote/voteStmt/daily')}}" method="post"
                  class="form-horizontal">
                {{csrf_field()}}

                <div class="Huiform text-r mt-10">
                    <span class="ml-5">统计日期：</span>
                    <input id="date_at" name="date_at" type="date" class="input-text"
                           style="width: 150px;"
                           value="{{ isset($con_arr['date_at']) ? $con_arr['date_at'] : '' }}"
                           placeholder="请输入统计日期">
                    <button type="submit" class="btn btn-success" id="" name="">
                        <i class="Hui-iconfont">&#xe665;</i> 搜索
                    </button>
                </div>
            </form>
        </div>

        {{--当天结束大赛列表--}}
        <div class="panel panel-primary mt-20">
            <div class="panel-header">当日结束大赛列表</div>
            <div class="panel-body">
                <table class="table table-border table-bordered table-bg table-sort mt-10">
                    <thead>
                    <tr class="text-c">
                        {{--<th width="25"><input type="checkbox" name="" value=""></t5h>--}}
                        <th width="200">ID/名称</th>
                        <th width="50">选手数</th>
                        <th width="50">投票数</th>
                        <th width="50">展示数</th>
                        <th width="50">分享数</th>
                        <th width="50">礼物金额</th>
                        <th width="50">报名状态</th>
                        <th width="50">投票状态</th>
                        <th width="50">激活状态</th>
                        <th width="50">冻结状态</th>
                        <th width="50">是否结算</th>
                    </tr>
                    </thead>
                    @foreach($voteActivities as $voteActivity)
                        <tr class="text-c">
                            <td>
                                <span class="c-primary" style="cursor: pointer;"
                                      onclick="creatIframe('{{URL::asset('/admin/vote/voteActivity/index')}}?id={{$voteActivity->id}}', '大赛信息-{{$voteActivity->name}}');">
                                {{$voteActivity->name}}({{$voteActivity->id}})
                                </span>
                            </td>
                            <td>{{$voteActivity->join_num}}</td>
                            <td>{{$voteActivity->vote_num}}</td>
                            <td>{{$voteActivity->show_num}}</td>
                            <td>{{$voteActivity->share_num}}</td>
                            <td><span class="c-primary">{{$voteActivity->gift_money}}</span></td>
                            <td>{{$voteActivity->apply_status_str}}</td>
                            <td>{{$voteActivity->vote_status_str}}</td>
                            <td>{{$voteActivity->valid_status_str}}</td>
                            <td>{{$voteActivity->status_str}}</td>
                            <td><span class="c-primary">{{$voteActivity->is_settle_str}}</span></td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>


        {{--当天收益信息--}}
        <div class="panel panel-primary mt-20">
            <div class="panel-header">当日各个地推团队收益信息
                <span class="r ml-10">当日总收益：<strong>{{$daily_total_money}}</strong> 元</span>
                <span class="r">当日所属地推团队活动总收益：<strong>{{$daily_team_total_money}}</strong> 元</span>
            </div>
            <div class="panel-body">
                <table class="table table-border table-bordered table-bg table-sort mt-10">
                    <thead>
                    <tr class="text-c">
                        {{--<th width="25"><input type="checkbox" name="" value=""></t5h>--}}
                        <th width="200">ID/名称</th>
                        <th width="50">省份</th>
                        <th width="50">城市</th>
                        <th width="60">联系人</th>
                        <th width="80">电话</th>
                        <th width="80">邮箱</th>
                        <th width="50">创建人员</th>
                        <th width="100">创建时间</th>
                        <th width="50">收益金额</th>
                    </tr>
                    </thead>
                    @foreach($voteTeams as $voteTeam)
                        <tr class="text-c">
                            <td>
                                <span class="c-primary" style="cursor: pointer;"
                                      onclick="creatIframe('{{URL::asset('/admin/vote/voteTeam/index')}}?id={{$voteTeam->id}}', '地推信息-{{$voteTeam->name}}');">
                                {{$voteTeam->name}}({{$voteTeam->id}})
                                </span>
                            </td>
                            <td>
                                {{$voteTeam->province?$voteTeam->province:'--'}}
                            </td>
                            <td>
                                {{$voteTeam->city?$voteTeam->city:'--'}}
                            </td>
                            <td>
                                {{$voteTeam->contact?$voteTeam->contact:'--'}}
                            </td>
                            <td>
                                {{$voteTeam->phonenum?$voteTeam->phonenum:'--'}}
                            </td>
                            <td>
                                {{$voteTeam->email?$voteTeam->email:'--'}}
                            </td>
                            <td>
                                {{$voteTeam->admin->name}}
                            </td>
                            <td>{{$voteTeam->created_at}}</td>
                            <td><span class="c-primary">{{$voteTeam->daily_total_money}}</span></td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">


        $(function () {

        });


    </script>
@endsection