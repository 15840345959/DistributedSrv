@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 打赏明细管理 <span
                class="c-gray en">&gt;</span> 打赏明细列表 <a class="btn btn-success radius r btn-refresh"
                                                        style="line-height:1.6em;margin-top:3px"
                                                        title="刷新"
                                                        href="javascript:location.replace(location.href);" title="刷新"
                                                        onclick="location.replace('{{URL::asset('/admin/vote/voteOrder/index')}}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>

    <div class="page-container">

        <div class="text-c">
            <form id="search_form" action="{{URL::asset('/admin/vote/voteOrder/index')}}" method="post"
                  class="form-horizontal">
                {{csrf_field()}}
                <div class="Huiform text-r">
                    <input id="search_word" name="search_word" type="text" class="input-text ml-5" style="width:250px"
                           placeholder="根据订单号进行检索" value="{{$con_arr['search_word']}}">
                    <span>选手id</span>
                    <input id="vote_user_id" name="vote_user_id" type="text" class="input-text"
                           style="width:100px"
                           placeholder="选手id" value="{{$con_arr['vote_user_id']}}">
                    <span class="ml-10">打赏人id</span>
                    <input id="user_id" name="user_id" type="text" class="input-text" style="width:100px"
                           placeholder="打赏人id" value="{{$con_arr['user_id']}}">
                    <span class="ml-10">大赛id</span>
                    <input id="activity_id" name="activity_id" type="text" class="input-text"
                           style="width:100px"
                           placeholder="大赛id" value="{{$con_arr['activity_id']}}">
                    <span class="select-box" style="width: 140px;">
                        <select id="pay_status" name="pay_status" class="select">
                            <option value="">全部状态</option>
                            @foreach(\App\Components\Utils::VOTE_ORDER_PAY_STATUS_VAL as $key=>$value)
                                <option value="{{$key}}" {{$con_arr['pay_status']==strval($key)?'selected':''}}>{{$value}}</option>
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
                    <th scope="col" colspan="11">打赏明细列表<span
                                class="r">共有数据：<strong>{{$datas->total()}}</strong> 条</span></th>
                </tr>
                <tr class="text-c">
                    {{--<th width="25"><input type="checkbox" name="" value=""></th>--}}
                    <th width="50">ID</th>
                    <th width="50">订单号</th>
                    <th width="100">大赛名称</th>
                    <th width="100">选手</th>
                    <th width="100">打赏人</th>
                    <th width="50">礼物</th>
                    <th width="50">数量</th>
                    <th width="50">金额</th>
                    <th width="50">支付状态</th>
                    <th width="50">支付时间</th>
                    <th width="50">操作</th>
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
                                  onclick="creatIframe('{{URL::asset('/admin/vote/voteActivity/index')}}?id={{$data->activity->id}}', '大赛信息-{{$data->activity->name}}');">{{$data->activity->name}}
                                ({{$data->activity->id}})</span>
                        </td>
                        <td>
                            <span class="c-primary" style="cursor: pointer;"
                                  onclick="creatIframe('{{URL::asset('/admin/vote/voteUser/index')}}?vote_user_id={{$data->vote_user->id}}', '选手信息-{{$data->vote_user->name}}');">{{$data->vote_user->name}}
                                ({{$data->vote_user->id}})</span>
                        </td>
                        <td>
                            <span class="c-primary" style="cursor: pointer;"
                                  onclick="creatIframe('{{URL::asset('/admin/user/info')}}?id={{$data->user->id}}', '用户信息-{{$data->user->nick_name}}');">{{$data->user->nick_name}}
                                ({{$data->user->id}})</span>
                        </td>
                        <td>
                            <span>{{$data->gift->name}}({{$data->gift->id}})</span>
                        </td>
                        <td>
                            <span>{{$data->gift_num}}</span>
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
                        <td>
                            <a style="text-decoration:none" onClick="refund(this,'{{$data->id}}')"
                               href="javascript:;" class="c-primary"
                               title="退款">
                                退款
                            </a>
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


        //点击退款
        function refund(obj, id) {
            consoledebug.log("refund id:" + id);
            layer.confirm('确认要退款吗？', function (index) {
                //此处请求后台程序，下方是成功后的前台处理
                var param = {
                    id: id,
                    pay_status: 4,
                    _token: "{{ csrf_token() }}"
                }
                //从后台设置管理员状态
                voteOrder_setStatus('{{URL::asset('')}}', param, function (ret) {
                    if (ret.status == true) {
                        $("#search_form").submit();
                    }
                })
                layer.msg('已退款', {icon: 1, time: 1000});
            });
        }

    </script>
@endsection