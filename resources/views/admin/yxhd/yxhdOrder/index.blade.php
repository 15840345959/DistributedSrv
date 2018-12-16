@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 抽奖订单管理 <span
                class="c-gray en">&gt;</span> 抽奖订单列表 <a class="btn btn-success radius r btn-refresh"
                                                        style="line-height:1.6em;margin-top:3px"
                                                        title="刷新"
                                                        onclick="location.replace('{{URL::asset('/admin/yxhd/yxhdOrder/index')}}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>
    <div class="page-container">

        <div class="text-c">
            <form id="search_form" action="{{URL::asset('/admin/yxhd/yxhdOrder/index')}}" method="post"
                  class="form-horizontal">
                {{csrf_field()}}
                <div class="Huiform text-r">
                    <input id="search_word" name="search_word" type="text" class="input-text" style="width:250px"
                           placeholder="根据订单号检索" value="{{$con_arr['search_word']}}">
                    <input id="id" name="id" type="text" class="input-text" style="width:150px"
                           placeholder="抽奖订单id" value="{{$con_arr['id']}}">
                    <input id="user_id" name="user_id" type="text" class="input-text" style="width:150px"
                           placeholder="用户id" value="{{$con_arr['user_id']}}">
                    <input id="activity_id" name="activity_id" type="text" class="input-text" style="width:150px"
                           placeholder="活动id" value="{{$con_arr['activity_id']}}">
                    <span class="select-box" style="width: 120px;">
                        <select id="winning_status" name="winning_status" class="select">
                            <option value="">请选择</option>
                            @foreach(\App\Components\Utils::YXHD_ORDER_WINNING_STATUS_VAL as $key=>$value)
                                <option value="{{$key}}" {{$con_arr['winning_status']==strval($key)?'selected':''}}>{{$value}}</option>
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
                    <th scope="col" colspan="9">抽奖订单列表
                        <span class="r">共有数据：<strong>{{$datas->total()}}</strong> 条</span></th>
                </tr>
                <tr class="text-c">
                    {{--<th width="25"><input type="checkbox" name="" value=""></th>--}}
                    <th width="20">ID</th>
                    <th width="100">订单号</th>
                    <th width="50">抽奖用户</th>
                    <th width="100">参与活动</th>
                    <th width="50">消耗积分</th>
                    <th width="50">抽奖时间</th>
                    <th width="50">中奖信息</th>
                    <th width="50">中奖状态</th>
                    <th width="40">操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($datas as $data)
                    <tr class="text-c">
                        <td>
                            <span>{{$data->id}}</span>
                        </td>
                        <td>
                            <span class="c-primary">{{$data->trade_no}}</span>
                        </td>
                        <td>
                            <img src="{{$data->user->avatar}}" style="width: 20px;">
                            <span class="ml-5 c-primary" style="cursor: pointer;"
                                  onclick="creatIframe('{{URL::asset('/admin/user/info')}}?id={{$data->user->id}}', '用户详情-{{$data->user->nick_name}}');">
                                {{$data->user->nick_name}}({{$data->user->id}})</span>
                        </td>
                        <td>
                            <span class="c-primary" style="cursor: pointer;"
                                  onclick="creatIframe('{{URL::asset('/admin/yxhd/yxhdActivity/index')}}?id={{$data->activity->id}}', '营销活动-{{$data->activity->name}}');">
                                {{$data->activity->name}}({{$data->activity->id}})</span>
                        </td>
                        <td class="">
                            <span>{{$data->total_score}}</span>
                        </td>
                        <td class="">
                            <span>{{$data->pay_at}}</span>
                        </td>
                        <td class="">
                            <span>{{isset($data->prize)?$data->prize->name:'--'}}</span>
                        </td>
                        <td class="">
                            <span class="label label-success radius">{{$data->winning_status_str}}</span>
                        </td>
                        <td class="td-manage">
                            <div>
                                <a title="详情" href="javascript:;"
                                   onclick="info('查看订单详情-{{$data->name}}','{{URL::asset('/admin/yxhd/yxhdOrder/info')}}?id={{$data->id}})',{{$data->id}})"
                                   class="c-primary ml-5" style="text-decoration:none">
                                    详情
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


    </script>
@endsection