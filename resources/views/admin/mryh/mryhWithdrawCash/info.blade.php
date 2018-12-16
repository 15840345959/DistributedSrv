@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 提现详细信息 <span
                class="c-gray en">&gt;</span> 提现详情 <a class="btn btn-success radius r btn-refresh"
                                                      style="line-height:1.6em;margin-top:3px"
                                                      href="javascript:location.replace(location.href);" title="刷新"
                                                      onclick="location.replace('{{URL::asset('/admin/mryh/mryhWithdrawCash/info')}}?id={{$data->id}}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>
    <div class="page-container">

        {{--提现基础信息--}}
        <div class="panel panel-primary mt-20">
            <div class="panel-header">基础信息</div>
            <div class="panel-body">
                <table class="table table-border table-bordered radius">
                    <tbody>
                    <tr>
                        <td>订单号</td>
                        <td class="c-primary">{{isset($data->trade_no)?$data->trade_no:'--'}}</td>
                        <td>用户id</td>
                        <td>
                            {{$data->user_id}}
                        </td>
                        <td>openid</td>
                        <td>{{$data->openid}}</td>
                        <td>提现时间</td>
                        <td>{{$data->withdraw_at}}</td>
                    </tr>
                    <tr>
                        <td>提现金额</td>
                        <td>{{isset($data->amount)?$data->amount:'--'}}元</td>
                        <td>支付时间</td>
                        <td>
                            {{$data->pay_at}}
                        </td>
                        <td>付款描述</td>
                        <td>{{$data->desc}}</td>
                        <td>提现状态</td>
                        <td>{{$data->withdraw_status_str}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{--提现基础信息--}}
        <div class="panel panel-primary mt-20">
            <div class="panel-header">用户信息</div>
            <div class="panel-body">

                <table class="table table-border table-bordered radius">
                    <tbody>
                    <tr>
                        <td rowspan="6" style="text-align: center;width: 120px;">
                            <img src="{{ $data->user->avatar ? $data->user->avatar.'?imageView2/1/w/200/h/200/interlace/1/q/75|imageslim' : URL::asset('/img/default_headicon.png')}}"
                                 style="width: 80px;height: 80px;">
                        </td>
                        <td>ID</td>
                        <td>{{isset($data->user->id)?$data->user->id:'--'}}</td>
                        <td>昵称</td>
                        <td>{{isset($data->user->nick_name)?$data->user->nick_name:'--'}}</td>
                        <td>姓名</td>
                        <td>{{isset($data->user->real_name)?$data->user->real_name:'--'}}</td>
                    </tr>
                    <tr>
                        <td>联系电话</td>
                        <td class="c-primary">{{isset($data->user->phonenum)?$data->user->phonenum:'--'}}</td>
                        <td>性别</td>
                        <td>
                            {{$data->user->gender_str}}
                        </td>
                        <td>注册时间</td>
                        <td>{{$data->user->created_at}}</td>
                    </tr>
                    <tr>
                        <td>状态</td>
                        <td class="c-primary">{{$data->user->status_str}}</td>
                        <td>语言</td>
                        <td>{{isset($data->user->language)?$data->user->language:'--'}}</td>
                        <td>签名</td>
                        <td>{{isset($data->user->sign)?$data->user->sign:'--'}}</td>
                    </tr>
                    <tr>
                        <td>国家</td>
                        <td>{{isset($data->user->country)?$data->user->country:'--'}}</td>
                        <td>省份</td>
                        <td>{{isset($data->user->province)?$data->user->province:'--'}}</td>
                        <td>城市</td>
                        <td>{{isset($data->user->city)?$data->user->city:'--'}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{--提现明细信息--}}
        <div class="ml-15 mt-20">
            <span class="c-danger">！结算数据非常重要，如有疑问，请一定联系TerryQi和阿伟进行处理，切不可猜想，一定将问题暴露。</span>
        </div>
        <table class="table table-border table-bordered table-bg table-sort mt-10">
            <thead>
            <tr class="text-c">
                {{--<th width="25"><input type="checkbox" name="" value=""></th>--}}
                <th width="40">ID</th>
                <th width="100">活动名称</th>
                <th width="80">参赛时间</th>
                <th width="60">参赛结果</th>
                <th width="100">参与天数</th>
                <th width="100">作品数</th>
                <th width="100">结算状态</th>
                <th width="100">结算时间</th>
                <th width="100">结算金额</th>
            </tr>
            </thead>
            @foreach($data->mryhJoins as $mryhJoin)
                <tr class="text-c">
                    {{--<td><input type="checkbox" value="1" name=""></td>--}}
                    <td>{{$mryhJoin->id}}</td>
                    <td>{{$mryhJoin->game->name}}</td>
                    <td>{{$mryhJoin->join_time}}</td>

                    <td>{{$mryhJoin->game_status_str}}</td>
                    <td>{{$mryhJoin->join_day_num}}</td>
                    <td>{{$mryhJoin->work_num}}</td>
                    <td>{{$mryhJoin->jiesuan_status_str}}</td>
                    <td>{{$mryhJoin->jiesuan_time}}</td>
                    <td>{{$mryhJoin->jiesuan_price}}</td>
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