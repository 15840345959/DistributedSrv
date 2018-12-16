@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 参与活动订单管理 <span
                class="c-gray en">&gt;</span> 参与活动订单列表 <a class="btn btn-success radius r btn-refresh"
                                                          style="line-height:1.6em;margin-top:3px"
                                                          title="刷新"
                                                          onclick="location.replace('{{URL::asset('/admin/mryh/mryhJoinOrder/index')}}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>
    <div class="page-container">

        <div class="text-c">
            <form id="search_form" action="{{URL::asset('/admin/mryh/mryhJoinOrder/index')}}" method="post"
                  class="form-horizontal">
                {{csrf_field()}}
                <div class="Huiform text-r">
                    <input id="search_word" name="search_word" type="text" class="input-text ml-5" style="width:250px"
                           placeholder="根据订单号进行检索" value="{{$con_arr['search_word']}}">
                    <span>用户id</span>
                    <input id="user_id" name="user_id" type="text" class="input-text"
                           style="width:100px"
                           placeholder="用户id" value="{{$con_arr['user_id']}}">
                    <span class="ml-10">活动id</span>
                    <input id="game_id" name="game_id" type="text" class="input-text"
                           style="width:100px"
                           placeholder="活动id" value="{{$con_arr['game_id']}}">
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
                    <th scope="col" colspan="7">参与活动订单列表
                        <span class="r">共有数据：<strong>{{$datas->total()}}</strong> 条</span></th>
                </tr>
                <tr class="text-c">
                    {{--<th width="25"><input type="checkbox" name="" value=""></th>--}}
                    <th width="50">ID</th>
                    <th width="50">订单号</th>
                    <th width="100">活动名称</th>
                    <th width="100">用户</th>
                    <th width="50">金额</th>
                    <th width="50">支付状态</th>
                    <th width="50">支付时间</th>
                </tr>
                </thead>
                <tbody>
                @foreach($datas as $data)
                    <tr class="text-c">
                        <td>
                            <span>{{$data->id}}</span>
                        </td>
                        <td>
                            <span>{{$data->trade_no}}</span>
                        </td>
                        <td>
                            <span class="c-primary" style="cursor: pointer;"
                                  onclick="creatIframe('{{URL::asset('/admin/mryh/mryhGame/index')}}?id={{$data->game->id}}', '活动信息-{{$data->game->name}}');">{{$data->game->name}}
                                ({{$data->game->id}})</span>
                        </td>
                        <td>
                            <span class="c-primary" style="cursor: pointer;"
                                  onclick="creatIframe('{{URL::asset('/admin/user/info')}}?id={{$data->user->id}}', '选手信息-{{$data->user->nick_name}}');">{{$data->user->nick_name}}
                                ({{$data->user->id}})</span>
                        </td>
                        <td>
                            <span class="c-primary">{{$data->total_fee}}</span>
                        </td>
                        <td>
                            <span>{{$data->pay_status_str}}</span>
                        </td>
                        <td>
                            <span>{{$data->pay_at?$data->pay_at:'--'}}</span>
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