@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span>业务概览
        <span class="c-gray en">&gt;</span>综合统计 <a class="btn btn-success radius r btn-refresh"
                                                   style="line-height:1.6em;margin-top:3px"
                                                   href="javascript:location.replace(location.href);"
                                                   title="刷新"
                                                   onclick="location.replace('{{URL::asset('/admin/stmt/index')}}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>
    {{--/{{$top_invite_users->type_id}}--}}
    <div class="page-container">
        <div class="text-c">
            <form action="{{URL::asset('/admin/stmt/index')}}" method="get" class="form-horizontal">
                {{csrf_field()}}
                <div class="Huiform text-r">
                    <input id="start_time" name="start_time" type="date" class="input-text" style="width:150px"
                           value="{{$con_arr['start_time']==null?'':$con_arr['start_time']}}">
                    <input id="end_time" name="end_time" type="date" class="input-text" style="width:150px"
                           value="{{$con_arr['end_time']==null?'':$con_arr['end_time']}}">
                    <button name="" id="" class="btn btn-success" type="submit"><i class="Hui-iconfont"></i> 搜索
                    </button>
                </div>
            </form>
        </div>

        <table class="table table-border table-bordered table-bg table-sort mt-20">
            <thead>
            <tr>
                <th>新增用户总量 {{$user_total_num}}户 (增长趋势图如下)</th>
            </tr>
            </thead>
            <tr>
                <td>
                    <div id="user_increase_trend_div" style="width: 100%;height: 200px;">

                    </div>
                </td>
            </tr>
        </table>

        <table class="table table-border table-bordered table-bg table-sort mt-20">
            <thead>
            <tr>
                <th scope="col" colspan="11">TOP10邀请成功 用户列表</th>
            </tr>
            <tr class="text-c">
                <th width="30">ID</th>
                <th width="80">头像</th>
                <th width="100">昵称</th>
                <th width="100">openid</th>
                {{--<th width="100">是否关注</th>--}}
                <th width="80">当前邀请数</th>
                <th width="80">目标邀请数</th>
                <th width="120">注册时间</th>
            </tr>
            </thead>
            <tbody>
            @foreach($top_invite_users as $data)
                <tr class="text-c">
                    <td>{{$data->id}}</td>
                    <td>
                        <img src="{{ $data->avatar ? $data->avatar.'?imageView2/1/w/200/h/200/interlace/1/q/75|imageslim' : URL::asset('/img/default_headicon.png')}}"
                             class="img-rect-30 radius-5">
                    </td>
                    <td>
                        <a style="text-decoration:none"
                           onClick="show_user('用户详情','{{URL::asset('/admin/user/info')}}?user_id={{$data->id}})',{{$data->id}})"
                           href="javascript:;"
                           title="用户详情">
                            {{$data->nick_name}}
                        </a>
                    </td>
                    <td>{{$data->fwh_openid}}</td>
                    {{--<td>{{$data->is_subscribe=="1"?"已经关注":"已经取消"}}</td>--}}
                    <td>{{$data->yq_num}}</td>
                    <td>{{$data->target_yq_num==0?'暂未生成海报':$data->target_yq_num}}</td>
                    <td>{{$data->created_at}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <table class="table table-border table-bordered table-bg table-sort mt-20">
            <thead>
            <tr>
                <th>共计邀请用户：{{$invite_user_total_num}}户（邀请趋势如下）</th>
            </tr>
            </thead>
            <tr>
                <td>
                    <div id="invite_user_increase_trend_div" style="width: 100%;height: 300px;">

                    </div>
                </td>
            </tr>
        </table>


        <table class="table table-border table-bordered table-bg table-sort mt-20">
            <thead>
            <tr>
                <th>邀请码情况概览 78元收费邀请码数：{{$charge_invite_code_num}}个 共计收入：{{$total_income}}元
                    免费邀请码数：{{$free_invite_code_num}}</th>
            </tr>
            </thead>
            <tr>
                <td>
                    <div id="invite_code_increase_trend_div" style="width: 100%;height: 300px;">

                    </div>
                </td>
            </tr>
        </table>
    </div>

@endsection

@section('script')

    <script type="text/javascript">

        //趋势类数据
        var user_increase_trend = {!!$user_increase_trend!!};
        var invite_user_increase_trend = {!!$invite_user_increase_trend!!};
        var free_invite_code_increase_trend = {!!$free_invite_code_increase_trend!!};
        var charge_invite_code_increase_trend = {!!$charge_invite_code_increase_trend!!};

        //用户增长趋势
        function showUserIncreaseTrendBarChart() {
            var chart = echarts.init(document.getElementById('user_increase_trend_div'));
            var date_arr = [];
            var date_value_arr = [];
            for (var i = 0; i < user_increase_trend.length; i++) {
                date_arr.push(user_increase_trend[i].tjdate);
                date_value_arr.push(user_increase_trend[i].nums);
            }
            var option = {
                color: ['#3398DB'],
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {            // 坐标轴指示器，坐标轴触发有效
                        type: 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
                    }
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: [
                    {
                        type: 'category',
                        data: date_arr,
                        axisTick: {
                            alignWithLabel: true
                        }
                    }
                ],
                yAxis: [
                    {
                        type: 'value'
                    }
                ],
                series: [
                    {
                        name: '',
                        type: 'bar',
                        barWidth: '40%',
                        data: date_value_arr
                    }
                ]
            };
            chart.setOption(option);
        }

        //邀请用户增长数
        function showInviteUserIncreaseTrendBarChart() {
            var chart = echarts.init(document.getElementById('invite_user_increase_trend_div'));
            var date_arr = [];
            var date_value_arr = [];
            for (var i = 0; i < invite_user_increase_trend.length; i++) {
                date_arr.push(invite_user_increase_trend[i].tjdate);
                date_value_arr.push(invite_user_increase_trend[i].nums);
            }
            var option = {
                color: ['#3398DB'],
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {            // 坐标轴指示器，坐标轴触发有效
                        type: 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
                    }
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: [
                    {
                        type: 'category',
                        data: date_arr,
                        axisTick: {
                            alignWithLabel: true
                        }
                    }
                ],
                yAxis: [
                    {
                        type: 'value'
                    }
                ],
                series: [
                    {
                        name: '',
                        type: 'bar',
                        barWidth: '40%',
                        data: date_value_arr
                    }
                ]
            };
            chart.setOption(option);
        }


        //邀请码趋势
        function showInviteCodeIncreaseTrendLineChart() {
            var chart = echarts.init(document.getElementById('invite_code_increase_trend_div'));

            var date_arr = [];
            var free_invite_code_trend_arr = [];
            var charge_invite_code_trend_arr = [];
            for (var i = 0; i < free_invite_code_increase_trend.length; i++) {
                date_arr.push(free_invite_code_increase_trend[i].tjdate);
                free_invite_code_trend_arr.push(free_invite_code_increase_trend[i].nums);
                charge_invite_code_trend_arr.push(charge_invite_code_increase_trend[i].nums);
            }

            var option = {
                title: {},
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    data: ['免费邀请码', '78元邀请码']
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                toolbox: {
                    feature: {
                        saveAsImage: {}
                    }
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: date_arr
                },
                yAxis: {
                    type: 'value'
                },
                series: [
                    {
                        name: '免费邀请码',
                        type: 'line',
                        stack: '日生成量',
                        data: free_invite_code_trend_arr
                    },
                    {
                        name: '78元邀请码',
                        type: 'line',
                        stack: '日生成量',
                        data: charge_invite_code_trend_arr
                    }
                ]
            };
            chart.setOption(option);
        }


        $(function () {
            showUserIncreaseTrendBarChart();
            showInviteUserIncreaseTrendBarChart();
            showInviteCodeIncreaseTrendLineChart();
        });

        //展示用户详情
        function show_user(title, url, id) {
            console.log("show_user url:" + url);
            var index = layer.open({
                type: 2,
                title: title,
                content: url
            });
            layer.full(index);
        }


    </script>
@endsection