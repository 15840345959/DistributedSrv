@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 地推详细信息 <span
                class="c-gray en">&gt;</span> 地推详情 <a class="btn btn-success radius r btn-refresh"
                                                      style="line-height:1.6em;margin-top:3px"
                                                      href="javascript:location.replace(location.href);" title="刷新"
                                                      onclick="location.replace('{{URL::asset('/admin/vote/voteTeam/info')}}?id={{$data->id}}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>
    <div class="page-container">

        {{--地推基础信息--}}
        <div class="panel panel-primary mt-20">
            <div class="panel-header">基础信息</div>
            <div class="panel-body">

                <table class="table table-border table-bordered radius">
                    <tbody>
                    <tr>
                        <td>ID</td>
                        <td>{{isset($data->id)?$data->id:'--'}}</td>
                        <td>名称</td>
                        <td>{{isset($data->name)?$data->name:'--'}}</td>
                        <td>省份</td>
                        <td>{{isset($data->province)?$data->province:'--'}}</td>
                        <td>城市</td>
                        <td>{{isset($data->city)?$data->city:'--'}}</td>
                    </tr>
                    <tr>
                        <td>联系人</td>
                        <td>{{isset($data->contact)?$data->contact:'--'}}</td>
                        <td>联系邮箱</td>
                        <td>{{isset($data->email)?$data->email:'--'}}</td>
                        <td>联系电话</td>
                        <td>{{isset($data->phonenum)?$data->phonenum:'--'}}</td>
                        <td>创建时间</td>
                        <td>{{isset($data->created_at)?$data->created_at:'--'}}</td>
                    </tr>
                    <tr>
                        <td>状态</td>
                        <td class="c-primary">{{$data->status_str}}</td>
                        <td>管理员</td>
                        <td>{{isset($data->admin)?$data->admin->name:'--'}}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>


        {{--近期大赛信息--}}
        <div class="cl pd-5 bg-1 bk-gray mt-20">
            <span class="l">
                 <a href="javascript:;"
                    onclick="creatIframe('{{URL::asset('/admin/vote/voteActivity/index')}}?vote_team_id={{$data->id}}', '地推团队-{{$data->name}}');"
                    class="btn btn-primary radius">
                     查看全部地推活动-详细信息
                 </a>
            </span>
        </div>
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
                              onclick="creatIframe('{{URL::asset('/admin/vote/voteActivity/index')}}?id={{$voteActivity->id}}', '活动信息-{{$voteActivity->name}}');">
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
@endsection

@section('script')
    <script type="text/javascript">


        $(function () {

        });


    </script>
@endsection