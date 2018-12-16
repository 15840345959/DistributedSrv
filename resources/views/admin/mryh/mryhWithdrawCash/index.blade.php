@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 参与信息 <span
                class="c-gray en">&gt;</span> 提现明细列表 <a class="btn btn-success radius r btn-refresh"
                                                        style="line-height:1.6em;margin-top:3px"
                                                        title="刷新"
                                                        onclick="location.replace('{{URL::asset('/admin/mryh/mryhWithdrawCash/index')}}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>
    <div class="page-container">

        <div class="text-c">
            <form id="search_form" action="{{URL::asset('/admin/mryh/mryhWithdrawCash/index')}}" method="post"
                  class="form-horizontal">
                {{csrf_field()}}
                <div class="Huiform text-r">
                    <span>用户id</span>
                    <input id="user_id" name="user_id" type="text" class="input-text"
                           style="width:100px"
                           placeholder="用户id" value="{{$con_arr['user_id']}}">
                    <input id="start_time" name="start_time" type="date" class="input-text ml-5" style="width:150px"
                           value="{{$con_arr['start_time']==null?'':$con_arr['start_time']}}">
                    <input id="end_time" name="end_time" type="date" class="input-text" style="width:150px"
                           value="{{$con_arr['end_time']==null?'':$con_arr['end_time']}}">
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
                    <th scope="col" colspan="9">提现明细列表
                        <span class="r">共有数据：<strong>{{$datas->total()}}</strong> 条</span></th>
                </tr>
                <tr class="text-c">
                    {{--<th width="25"><input type="checkbox" name="" value=""></th>--}}
                    <th width="20">ID</th>
                    <th width="80">流水号</th>
                    <th width="100">用户</th>
                    <th width="80">openid</th>
                    <th width="60">提现时间</th>
                    <th width="50">金额(元)</th>
                    <th width="60">支付时间</th>
                    <th width="40">状态</th>
                    <th width="150">关联参与记录</th>
                </tr>
                </thead>
                <tbody>
                @foreach($datas as $data)
                    <tr class="text-c">
                        <td>
                            <span>{{$data->id}}</span>
                        </td>
                        <td>
                            <span class="c-primary"
                                  onclick="creatIframe('{{URL::asset('/admin/mryh/mryhWithdrawCash/info')}}?id={{$data->id}}', '提现详情-{{$data->trade_no}}');">{{$data->trade_no}}</span>
                        </td>
                        <td>
                            <span class="c-primary"
                                  onclick="creatIframe('{{URL::asset('/admin/user/info')}}?id={{$data->user->id}}', '选手信息-{{$data->user->nick_name}}');">{{$data->user->nick_name}}
                                ({{$data->user->id}})</span>
                        </td>
                        <td>
                            {{$data->openid}}
                        </td>
                        <td>
                            {{$data->withdraw_at}}
                        </td>
                        <td>
                            {{$data->amount}}
                        </td>
                        <td>
                            {{$data->pay_at?$data->pay_at:'--'}}
                        </td>
                        <td>
                            <span class="c-primary">{{$data->withdraw_status_str}}</span>
                        </td>
                        <td>
                            @foreach($data->mryhJoins as $mryhJoin)
                                <div class="mb-5">
                                    {{$mryhJoin->game->name}}/{{$mryhJoin->jiesuan_price}}
                                </div>
                            @endforeach
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