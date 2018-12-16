@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 订单明细管理 <span
                class="c-gray en">&gt;</span> 订单明细列表 <a class="btn btn-success radius r btn-refresh"
                                                        style="line-height:1.6em;margin-top:3px"
                                                        title="刷新"
                                                        href="javascript:location.replace(location.href);" title="刷新"
                                                        onclick="location.replace('{{URL::asset('/admin/shop/shopOrder/index')}}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>

    <div class="page-container">

        <div class="text-c">
            <form id="search_form" action="{{URL::asset('/admin/shop/shopOrder/index')}}" method="post"
                  class="form-horizontal">
                {{csrf_field()}}
                <div class="Huiform text-r">
                    <input id="search_word" name="search_word" type="text" class="input-text ml-5" style="width:250px"
                           placeholder="根据订单号进行检索" value="{{$con_arr['search_word']}}">
                    <span class="ml-10">商品id</span>
                    <input id="goods_id" name="goods_id" type="text" class="input-text"
                           style="width:100px"
                           placeholder="商品id" value="{{$con_arr['goods_id']}}">
                    <span class="ml-10">买家id</span>
                    <input id="user_id" name="user_id" type="text" class="input-text" style="width:100px"
                           placeholder="买家id" value="{{$con_arr['user_id']}}">
                    <span class="select-box" style="width: 140px;">
                        <select id="pay_status" name="pay_status" class="select">
                            <option value="">全部状态</option>
                            @foreach(\App\Components\Utils::SHOP_ORDER_PAY_STATUS_VAL as $key=>$value)
                                <option value="{{$key}}" {{$con_arr['pay_status']==strval($key)?'selected':''}}>{{$value}}</option>
                            @endforeach
                        </select>
                    </span>
                    <span class="select-box" style="width: 140px;">
                        <select id="send_status" name="send_status" class="select">
                            <option value="">全部状态</option>
                            @foreach(\App\Components\Utils::SHOP_ORDER_SEND_STATUS_VAL as $key=>$value)
                                <option value="{{$key}}" {{$con_arr['send_status']==strval($key)?'selected':''}}>{{$value}}</option>
                            @endforeach
                        </select>
                    </span>
                    <span class="select-box" style="width: 140px;">
                        <select id="refund_status" name="refund_status" class="select">
                            <option value="">全部状态</option>
                            @foreach(\App\Components\Utils::SHOP_ORDER_REFUND_STATUS_VAL as $key=>$value)
                                <option value="{{$key}}" {{$con_arr['refund_status']==strval($key)?'selected':''}}>{{$value}}</option>
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
                    <th scope="col" colspan="6">订单明细列表</th>
                </tr>
                <tr class="text-c">
                    {{--<th width="25"><input type="checkbox" name="" value=""></th>--}}
                    <th width="20">ID</th>
                    <th width="80">订单信息</th>
                    <th width="60">物流信息</th>
                    <th width="50">数量/金额</th>
                    <th width="50">订单状态</th>
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
                            <div><span class="c-primary">{{$data->trade_no}}</span></div>
                            <div class="mt-5">
                                <img src="{{ $data->goods->img.'?imageView2/1/w/120/h/60/interlace/1/q/75|imageslim'}}"/>
                            </div>
                            <div class="mt-5">
                            <span class="c-primary text-oneline" style="cursor: pointer;"
                                  onclick="creatIframe('{{URL::asset('/admin/goods/edit')}}?id={{$data->goods->id}}', '商品信息-{{$data->goods->name}}');">{{$data->goods->name}}
                                ({{$data->goods->id}})</span>
                            </div>
                            <div class="mt-5">
                               <span>{{$data->user->nick_name}}
                                   ({{$data->user->id}})</span>
                            </div>
                        </td>
                        <td>
                            <div>
                                {{$data->rec_name}}/{{$data->rec_tel}}/{{$data->rec_address}}
                            </div>
                            <div class="mt-5">
                                订单备注： <span>{{isset($data->remark)?$data->remark:'--'}}</span>
                            </div>
                        </td>
                        <td>
                            <span>{{$data->goods_num}}个/{{$data->total_fee}}元</span>
                        </td>
                        <td>
                            <div class="mt-5">
                                <span>支付状态：</span><span
                                        class="ml-5 c-primary label label-primary">{{$data->pay_status_str}}</span>
                            </div>
                            <div class="mt-5">
                                <span class="">发货状态：</span><span
                                        class="ml-5 label label-secondary">{{$data->send_status_str}}</span>
                            </div>
                            <div class="mt-5">
                                <span class="">退款状态：</span><span
                                        class="ml-5 label label-success">{{$data->refund_status_str}}</span>
                            </div>
                        </td>
                        <td>
                            <a style="text-decoration:none"
                               onclick="info('订单详情-{{$data->trade_no}}','{{URL::asset('/admin/shop/shopOrder/info')}}?id={{$data->id}})',{{$data->id}})"
                               href="javascript:;" class="c-primary"
                               title="发货/退款/备注">
                                发货/退款/备注
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


        //发货/退款/备注
        function info(title, url) {
            creatIframe(url, title)
        }

    </script>
@endsection