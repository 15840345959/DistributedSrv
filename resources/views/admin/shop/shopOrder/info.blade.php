@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 订单详细信息 <span
                class="c-gray en">&gt;</span> 订单详情 <a class="btn btn-success radius r btn-refresh"
                                                      style="line-height:1.6em;margin-top:3px"
                                                      href="javascript:location.replace(location.href);" title="刷新"
                                                      onclick="location.replace('{{URL::asset('/admin/shop/shopOrder/info')}}?id={{$data->id}}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>
    <div class="page-container">

        {{--订单基础信息--}}
        <div class="panel panel-primary mt-20">
            <div class="panel-header">订单详情</div>
            <div class="panel-body">

                <table class="table table-border table-bordered radius">
                    <tbody>
                    <tr>
                        <td>订单号</td>
                        <td>{{$data->trade_no}}</td>
                        <td>商品名称</td>
                        <td>
                            {{isset($data->goods->name)?$data->goods->name:'--'}}
                        </td>
                        <td>销售价格</td>
                        <td>{{isset($data->goods->price)?$data->goods->price:'--'}}元</td>
                    </tr>
                    <tr>
                        <td>订单金额</td>
                        <td>{{isset($data->total_fee)?$data->total_fee:'--'}}元</td>
                        <td>商品数量</td>
                        <td>
                            {{$data->goods_num}}个
                        </td>
                        <td>商品id</td>
                        <td>{{$data->goods_id}}</td>
                    </tr>
                    <tr>
                        <td>收件人</td>
                        <td class="c-primary"
                            onclick="userInfo('{{URL::asset('admin/user/info')}}?id={{$data->user_id}})','用户详情-{{$data->user->nick_name}}')">
                            {{isset($data->rec_name)?$data->rec_name:'--'}}({{$data->user_id}})
                        </td>
                        <td>联系电话</td>
                        <td>
                            {{$data->rec_tel}}
                        </td>
                        <td>收货地址</td>
                        <td>{{$data->rec_address}}</td>
                    </tr>
                    <tr>
                        <td>支付状态</td>
                        <td>{{$data->pay_status_str}}</td>
                        <td>支付方式</td>
                        <td>{{$data->pay_type_str}}</td>
                        <td>支付时间</td>
                        <td>{{isset($data->pay_at)?$data->pay_at:'--'}}</td>
                    </tr>
                    <tr>
                        <td>发货单号</td>
                        <td>{{isset($data->send_no)?$data->send_no:'--'}}</td>
                        <td>发货时间</td>
                        <td>{{isset($data->send_at)?$data->send_at:'--'}}</td>
                        <td>发货状态</td>
                        <td>{{isset($data->send_statuss_str)?$data->send_statuss_str:'--'}}</td>
                    </tr>
                    <tr>
                        <td>退款金额</td>
                        <td>{{isset($data->refund_fee)?$data->refund_fee:'--'}}</td>
                        <td>退款时间</td>
                        <td>{{isset($data->refund_at)?$data->refund_at:'--'}}</td>
                        <td>退款状态</td>
                        <td>{{isset($data->refund_status_str)?$data->refund_status_str:'--'}}</td>
                    </tr>
                    <tr>
                        <td>订单备注</td>
                        <td colspan="5">{{isset($data->remark)?$data->remark:'--'}}</td>
                    </tr>
                    </tbody>
                </table>
                {{--相关操作--}}
                <div class="cl pd-5 bg-1 bk-gray mt-20">
                    <span class="l">
                        <a href="javascript:;"
                           onclick="edit('添加参赛选手','{{URL::asset('/admin/vote/voteUser/edit')}}?activity_id={{$data->id}}')"
                           class="btn btn-primary radius">
                            订单备注操作
                        </a>
                    </span>
                            <span class="l ml-10">
                        <a href="javascript:;"
                           onclick="clickImportVoteUser('批量导入选手-图片','{{URL::asset('/admin/vote/voteActivity/importVoteUser')}}?activity_id={{$data->id}})',{{$data->id}})"
                           class="btn btn-primary radius">
                            订单发货管理
                        </a>
                    </span>
                            <span class="l ml-10">
                        <a href="javascript:;"
                           onclick="clickImportVoteUser('批量导入选手-视频','{{URL::asset('/admin/vote/voteActivity/importVoteUserVideo')}}?activity_id={{$data->id}})',{{$data->id}})"
                           class="btn btn-primary radius">
                           订单退款管理
                        </a>
                    </span>
                </div>
            </div>
        </div>


    </div>
@endsection

@section('script')
    <script type="text/javascript">


        $(function () {

        });


        /*
         * 跳转用户详情页面
         *
         * By TerryQi
         *
         * 2018-11-18
         */
        function userInfo(url, title) {
            creatIframe(url, title)
        }


    </script>
@endsection