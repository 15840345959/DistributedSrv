@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 参赛选手管理 <span
                class="c-gray en">&gt;</span> 参赛选手信息 <a class="btn btn-success radius r btn-refresh"
                                                        style="line-height:1.6em;margin-top:3px"
                                                        title="刷新"
                                                        onclick="location.replace('{{URL::asset('/admin/vote/voteUser/info')}}?id={{$data->id}}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>
    <div class="page-container">

        <div class="text-l">
            <button type="submit" class="btn btn-primary" id="" name=""
                    onclick="creatIframe('{{URL::asset('/admin/vote/voteRecord/index')}}?vote_user_id={{$data->id}}', '投票明细-{{$data->name}}');">
                投票明细
            </button>
            <button type="submit" class="btn btn-primary" id="" name=""
                    onclick="creatIframe('{{URL::asset('/admin/vote/voteShareRecord/index')}}?vote_user_id={{$data->id}}', '分享明细-{{$data->name}}');">
                分享明细
            </button>
            <button type="submit" class="btn btn-primary" id="" name=""
                    onclick="creatIframe('{{URL::asset('/admin/vote/voteGuanZhu/index')}}?vote_user_id={{$data->id}}', '关注明细-{{$data->name}}');">
                关注明细
            </button>
        </div>

        <div class="panel panel-primary mt-20">
            <div class="panel-header">选手信息</div>
            <div class="panel-body">
                <table class="table table-border table-bordered radius">
                    <tbody>
                    <tr>
                        <td>ID</td>
                        <td>{{isset($data->id)?$data->id:'--'}}</td>
                        <td>姓名</td>
                        <td>{{isset($data->name)?$data->name:'--'}}</td>
                        <td>编号</td>
                        <td>{{isset($data->code)?$data->code:'--'}}</td>
                        <td>报名时间</td>
                        <td>{{isset($data->created_at)?$data->created_at:'--'}}</td>
                    </tr>
                    <tr>
                        <td>昨日排名</td>
                        <td>{{isset($data->yes_pm)?$data->yes_pm:'--'}}</td>
                        <td>当前排名</td>
                        <td>{{isset($data->pm)?$data->pm:'--'}}</td>
                        <td>当前票数</td>
                        <td>{{isset($data->vote_num)?$data->vote_num:'--'}}</td>
                        <td>当前礼物数</td>
                        <td>{{isset($data->gift_money)?$data->gift_money:'--'}}</td>
                    </tr>
                    <tr>
                        <td>分享数</td>
                        <td>{{isset($data->share_num)?$data->share_num:'--'}}</td>
                        <td>被关注数</td>
                        <td>{{isset($data->fans_num)?$data->fans_num:'--'}}</td>
                        <td>展示数</td>
                        <td>{{isset($data->show_num)?$data->show_num:'--'}}</td>
                        <td>报名类型</td>
                        <td class="c-primary">{{isset($data->type_str)?$data->type_str:'--'}}</td>
                    </tr>
                    <tr>
                        <td>审核状态</td>
                        <td class="c-primary">{{isset($data->audit_status_str)?$data->audit_status_str:'--'}}</td>
                        <td>激活状态</td>
                        <td class="c-primary">{{isset($data->valid_status_str)?$data->valid_status_str:'--'}}</td>
                        <td>基本状态</td>
                        <td class="c-primary">{{isset($data->status_str)?$data->status_str:'--'}}</td>
                        <td>锁定状态</td>
                        <td class="c-primary">--</td>
                    </tr>
                    <tr>
                        <td>宣言</td>
                        <td colspan="7">{{isset($data->declaration)?$data->declaration:'--'}}</td>
                    </tr>
                    <tr>
                        <td>作品名称</td>
                        <td colspan="7">{{isset($data->work_name)?$data->work_name:'--'}}</td>
                    </tr>
                    <tr>
                        <td>作品介绍</td>
                        <td colspan="7">{{isset($data->work_desc)?$data->work_desc:'--'}}</td>
                    </tr>
                    <tr>
                        <td colspan="8">
                            <div class="row">
                                @foreach($data->img_arr as $img)
                                    <div class="col-md-4">
                                        <img src="{{$img}}" style="width: 90px;" class="pd-10">
                                    </div>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel panel-primary mt-20">
            <div class="panel-header">大赛信息</div>
            <div class="panel-body">
                <table class="table table-border table-bordered radius">
                    <tbody>
                    <tr>
                        <td>ID</td>
                        <td>{{isset($data->activity->id)?$data->activity->id:'--'}}</td>
                        <td>名称</td>
                        <td>
                            <a class="c-primary" style="text-decoration:none"
                               onclick="creatIframe('{{URL::asset('/admin/vote/voteActivity/edit')}}?id={{$data->activity->id}}', '大赛信息-{{$data->activity->name}}');">
                                {{isset($data->activity->name)?$data->activity->name:'--'}}
                            </a>
                        </td>
                        <td>活动编码</td>
                        <td>{{isset($data->activity->code)?$data->activity->code:'--'}}</td>
                        <td>创建时间</td>
                        <td>{{isset($data->activity->created_at)?$data->activity->created_at:'--'}}</td>
                    </tr>
                    <tr>
                        <td>参赛人数</td>
                        <td>{{isset($data->activity->join_num)?$data->activity->join_num:'--'}}</td>
                        <td>总投票数</td>
                        <td>{{isset($data->activity->vote_num)?$data->activity->vote_num:'--'}}</td>
                        <td>展示次数</td>
                        <td>{{isset($data->activity->show_num)?$data->activity->show_num:'--'}}</td>
                        <td>礼物总数</td>
                        <td>{{isset($data->activity->gift_money)?$data->activity->gift_money:'--'}}</td>
                    </tr>
                    <tr>
                        <td>分享总数</td>
                        <td>{{isset($data->activity->share_num)?$data->activity->share_num:'--'}}</td>
                        <td>投诉人数</td>
                        <td>{{isset($data->activity->complain_num)?$data->activity->complain_num:'--'}}</td>
                        <td>--</td>
                        <td>--</td>
                        <td>---</td>
                        <td>--</td>
                    </tr>
                    <tr>
                        <td>报名开始时间</td>
                        <td>{{isset($data->activity->apply_start_time)?$data->activity->apply_start_time:'--'}}</td>
                        <td>报名结束时间</td>
                        <td>{{isset($data->activity->apply_end_time)?$data->activity->apply_end_time:'--'}}</td>
                        <td>投票开始时间</td>
                        <td>{{isset($data->activity->vote_start_time)?$data->activity->vote_start_time:'--'}}</td>
                        <td>投票结束时间</td>
                        <td>{{isset($data->activity->vote_end_time)?$data->activity->vote_end_time:'--'}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        @if($data->user)
            <div class="panel panel-primary mt-20">
                <div class="panel-header">关联用户</div>
                <div class="panel-body">
                    <table class="table table-border table-bordered radius">
                        <tbody>
                        <tr>
                            <td>ID</td>
                            <td>{{isset($data->user->id)?$data->user->id:'--'}}</td>
                            <td>昵称</td>
                            <td>
                                <a class="c-primary" style="text-decoration:none"
                                   onclick="creatIframe('{{URL::asset('/admin/user/info')}}?id={{$data->user->id}}', '用户信息-{{$data->user->nick_name}}');">
                                    {{isset($data->user->nick_name)?$data->user->nick_name:'--'}}
                                </a>
                            </td>
                            <td>姓名</td>
                            <td>{{isset($data->user->real_name)?$data->user->real_name:'--'}}</td>
                            <td>手机号</td>
                            <td>{{isset($data->user->phonenum)?$data->user->phonenum:'--'}}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </div>
@endsection

@section('script')
    <script type="text/javascript">


        $(function () {

        });


    </script>
@endsection