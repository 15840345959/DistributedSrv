@extends('admin.layouts.app')

@section('style')
    <link rel="stylesheet" href="">
@endsection

@section('content')

    <style>

        .bg-aqua, .callout.callout-info, .alert-info, .label-info, .modal-info .modal-body {
            background-color: #00c0ef !important;
        }

        .bg-red, .bg-yellow, .bg-aqua, .bg-blue, .bg-light-blue, .bg-green, .bg-navy, .bg-teal, .bg-olive, .bg-lime, .bg-orange, .bg-fuchsia, .bg-purple, .bg-maroon, .bg-black, .bg-red-active, .bg-yellow-active, .bg-aqua-active, .bg-blue-active, .bg-light-blue-active, .bg-green-active, .bg-navy-active, .bg-teal-active, .bg-olive-active, .bg-lime-active, .bg-orange-active, .bg-fuchsia-active, .bg-purple-active, .bg-maroon-active, .bg-black-active, .callout.callout-danger, .callout.callout-warning, .callout.callout-info, .callout.callout-success, .alert-success, .alert-danger, .alert-error, .alert-warning, .alert-info, .label-danger, .label-info, .label-warning, .label-primary, .label-success, .modal-primary .modal-body, .modal-primary .modal-header, .modal-primary .modal-footer, .modal-warning .modal-body, .modal-warning .modal-header, .modal-warning .modal-footer, .modal-info .modal-body, .modal-info .modal-header, .modal-info .modal-footer, .modal-success .modal-body, .modal-success .modal-header, .modal-success .modal-footer, .modal-danger .modal-body, .modal-danger .modal-header, .modal-danger .modal-footer {
            color: #fff !important;
        }

        .small-box {
            border-radius: 2px;
            position: relative;
            display: block;
            margin-bottom: 20px;
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
        }

        .small-box > .inner {
            padding: 10px;
        }

        .small-box .icon {
            -webkit-transition: all .3s linear;
            -o-transition: all .3s linear;
            transition: all .3s linear;
            position: absolute;
            top: -10px;
            right: 10px;
            z-index: 0;
            font-size: 90px;
            color: rgba(0, 0, 0, 0.15);
        }

        .small-box > .small-box-footer {
            position: relative;
            text-align: center;
            padding: 3px 0;
            color: #fff;
            color: rgba(255, 255, 255, 0.8);
            display: block;
            z-index: 10;
            background: rgba(0, 0, 0, 0.1);
            text-decoration: none;
        }

        .small-box h3, .small-box p {
            z-index: 5;
        }

        .small-box h3 {
            font-size: 38px;
            font-weight: bold;
            margin: 0 0 10px 0;
            white-space: nowrap;
            padding: 0;
        }

        .small-box p {
            font-size: 15px;
        }

        .products-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .product-list-in-box > .item {
            -webkit-box-shadow: none;
            box-shadow: none;
            border-radius: 0;
            border-bottom: 1px solid #f4f4f4;
        }

        .products-list > .item {
            border-radius: 3px;
            -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
            padding: 10px 0;
            background: #fff;
        }

        .products-list .product-img {
            float: left;
        }

        .products-list .product-img img {
            width: 50px;
            height: 50px;
        }

        .products-list .product-info {

        }

        .products-list .product-title {
            font-weight: 600;
        }

        .bg-yellow, .callout.callout-warning, .alert-warning, .label-warning, .modal-warning .modal-body {
            background-color: #f39c12 !important;
        }

        .bg-red, .bg-yellow, .bg-aqua, .bg-blue, .bg-light-blue, .bg-green, .bg-navy, .bg-teal, .bg-olive, .bg-lime, .bg-orange, .bg-fuchsia, .bg-purple, .bg-maroon, .bg-black, .bg-red-active, .bg-yellow-active, .bg-aqua-active, .bg-blue-active, .bg-light-blue-active, .bg-green-active, .bg-navy-active, .bg-teal-active, .bg-olive-active, .bg-lime-active, .bg-orange-active, .bg-fuchsia-active, .bg-purple-active, .bg-maroon-active, .bg-black-active, .callout.callout-danger, .callout.callout-warning, .callout.callout-info, .callout.callout-success, .alert-success, .alert-danger, .alert-error, .alert-warning, .alert-info, .label-danger, .label-info, .label-warning, .label-primary, .label-success, .modal-primary .modal-body, .modal-primary .modal-header, .modal-primary .modal-footer, .modal-warning .modal-body, .modal-warning .modal-header, .modal-warning .modal-footer, .modal-info .modal-body, .modal-info .modal-header, .modal-info .modal-footer, .modal-success .modal-body, .modal-success .modal-header, .modal-success .modal-footer, .modal-danger .modal-body, .modal-danger .modal-header, .modal-danger .modal-footer {
            color: #fff !important;
        }

        .pull-right {
            float: right;
        }

        .pull-right {
            float: right !important;
        }

        .label-warning {
            background-color: #f0ad4e;
        }

        .label {
            display: inline;
            padding: .2em .6em .3em;
            font-size: 75%;
            font-weight: 700;
            line-height: 1;
            color: #fff;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: .25em;
            margin-right: 20px;
            margin-top: 5px;
        }

        .products-list .product-description {
            display: block;
            color: #999;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        ::-webkit-scrollbar-track-piece {
            background-color: #f8f8f8;
        }

        ::-webkit-scrollbar {
            width: 9px;
            height: 9px;
        }

        ::-webkit-scrollbar-thumb {
            background-color: #dddddd;
            background-clip: padding-box;
            min-height: 28px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background-color: #bbb;
        }
    </style>

    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 活动统计管理 <span
                class="c-gray en">&gt;</span> 活动统计信息 <a class="btn btn-success radius r btn-refresh"
                                                        style="line-height:1.6em;margin-top:3px"
                                                        title="刷新"
                                                        onclick="location.replace('{{URL::asset('/admin/vote/voteActivity/info')}}?id={{$data->id}}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>
    <div class="page-container">

        <div class="panel panel-primary mt-20">
            <div class="panel-header">大赛基础信息/地推团队信息/管理员信息</div>
            <div class="panel-body">
                <table class="table table-border table-bordered radius">
                    <tbody>
                    <tr>
                        <td>ID</td>
                        <td>{{isset($data->id)?$data->id:'--'}}</td>
                        <td>活动名称</td>
                        <td>{{isset($data->name)?$data->name:'--'}}</td>
                        <td>活动代码</td>
                        <td>{{isset($data->code)?$data->code:'--'}}</td>
                        <td>创建时间</td>
                        <td>{{isset($data->created_at)?$data->created_at:'--'}}</td>
                    </tr>
                    <tr>
                        <td>报名开始</td>
                        <td>{{isset($data->apply_start_time)?$data->apply_start_time:'--'}}</td>
                        <td>报名结束</td>
                        <td>{{isset($data->apply_end_time)?$data->apply_end_time:'--'}}</td>
                        <td>投票开始</td>
                        <td>{{isset($data->vote_start_time)?$data->vote_start_time:'--'}}</td>
                        <td>投票结束</td>
                        <td>{{isset($data->vote_end_time)?$data->vote_end_time:'--'}}</td>
                    </tr>
                    <tr>
                        <td>报名状态</td>
                        <td class="c-primary">{{isset($data->apply_status_str)?$data->apply_status_str:'--'}}</td>
                        <td>投票状态</td>
                        <td class="c-primary">{{isset($data->vote_status_str)?$data->vote_status_str:'--'}}</td>
                        <td>激活状态</td>
                        <td class="c-primary">{{isset($data->valid_status_str)?$data->valid_status_str:'--'}}</td>
                        <td>冻结状态</td>
                        <td class="c-primary">{{isset($data->status_str)?$data->status_str:'--'}}</td>
                    </tr>
                    <tr>
                        <td>参赛人数</td>
                        <td>{{isset($data->join_num)?$data->join_num:'--'}}</td>
                        <td>投票人数</td>
                        <td>{{isset($data->vote_num)?$data->vote_num:'--'}}</td>
                        <td>展示人数</td>
                        <td>{{isset($data->show_num)?$data->show_num:'--'}}</td>
                        <td>分享人数</td>
                        <td>{{isset($data->share_num)?$data->share_num:'--'}}</td>
                    </tr>
                    <tr>
                        <td>投诉人数</td>
                        <td>{{isset($data->complain_num)?$data->complain_num:'--'}}</td>
                        <td>礼物数</td>
                        <td>{{isset($data->gift_num)?$data->gift_num:'--'}}</td>
                        <td>礼物金额</td>
                        <td class="c-primary">{{isset($data->gift_money)?$data->gift_money:'--'}}</td>
                        <td>结算状态</td>
                        <td>{{isset($data->is_settle_str)?$data->is_settle_str:'--'}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>


        <div class="content" style="margin: 2rem;">

            <div class="panel panel-default">
                <div class="panel-header">投票/收入趋势图（<span id="vote_money_date_span"></span>） 共计投票数：<span
                            id="vote_total_num"></span>票 共计收入：<span
                            id="order_total_money"></span>元
                </div>
                <div class="panel-body">
                    <div class="btn-group" role="group" aria-label="...">
                        <button type="button" class="btn btn-default order-date-type active"
                                onclick="clickVoteMoneyData(7)">
                            近7天
                        </button>
                        <button type="button" class="btn btn-default vote-money-date-type"
                                onclick="clickVoteMoneyData(14)">近14天
                        </button>
                        <button type="button" class="btn btn-default vote-money-date-type"
                                onclick="clickVoteMoneyData(30)">近30天
                        </button>
                        <button type="button" class="btn btn-default vote-money-date-type"
                                onclick="clickVoteMoneyData(60)">近60天
                        </button>
                        <button type="button" class="btn btn-default vote-money-date-type"
                                onclick="clickVoteMoneyData(90)">近90天
                        </button>
                    </div>

                    <div id="vote_money_chart" style="height: 300px;">

                    </div>
                </div>
            </div>
        </div>


        <div class="content" style="margin: 2rem;">

            <div class="panel panel-default">
                <div class="panel-header">订单转化（<span id="order_date_span"></span>） 共有订单：<span
                            id="order_total_num"></span>笔 支付成功订单：<span
                            id="pay_order_total_num"></span>笔
                </div>
                <div class="panel-body">
                    <div class="btn-group" role="group" aria-label="...">
                        <button type="button" class="btn btn-default order-date-type active"
                                onclick="clickOrderData(7)">
                            近7天
                        </button>
                        <button type="button" class="btn btn-default vote-money-date-type"
                                onclick="clickOrderData(14)">近14天
                        </button>
                        <button type="button" class="btn btn-default vote-money-date-type"
                                onclick="clickOrderData(30)">近30天
                        </button>
                        <button type="button" class="btn btn-default vote-money-date-type"
                                onclick="clickOrderData(60)">近60天
                        </button>
                        <button type="button" class="btn btn-default vote-money-date-type"
                                onclick="clickOrderData(90)">近90天
                        </button>
                    </div>

                    <div id="order_chart" style="height: 300px;">

                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('script')

    <script type="text/javascript" src="{{asset('js/echarts.min.js')}}"></script>

    <script type="text/javascript">

        //大赛id
        var voteActivity_id = '{{$data->id}}';

        $(function () {
            clickVoteMoneyData(7);
            clickOrderData(7);
        });

        //点击投票数/金额数
        function clickVoteMoneyData(days_num) {
            $('.vote-money-date-type').removeClass('active')
            $(this).addClass('active')
            getVote_Money(days_num);
        }

        //点击订单趋势
        function clickOrderData(days_num) {
            $('.order-date-type').removeClass('active')
            $(this).addClass('active')
            getOrder(days_num);
        }

        //获取投票数和收入数的趋势图
        function getVote_Money(days_num) {
            ajaxRequest('{{URL::asset('/admin/vote/voteActivity/info/vote_money')}}', {
                "days_num": days_num,
                "id": voteActivity_id
            }, "GET", function (ret) {
                if (ret.result === true) {
                    var msgObj1 = ret.ret.vote_arr;
                    var msgObj2 = ret.ret.order_arr;
                    loadTwoLineChart('vote_money_chart', msgObj1, msgObj2, '投票数', '收入金额', '票', '元');
                    $("#vote_total_num").text(ret.ret.vote_total_num);
                    $("#order_total_money").text(ret.ret.order_total_money);
                    $('#vote_money_date_span').text(msgObj1[0]['date'] + '-' + msgObj1[msgObj1.length - 1]['date'])
                } else {

                }
            })
        }

        //获取订单数和支付成功订单数
        function getOrder(days_num) {
            ajaxRequest('{{URL::asset('/admin/vote/voteActivity/info/order')}}', {
                "days_num": days_num,
                "id": voteActivity_id
            }, "GET", function (ret) {
                if (ret.result === true) {
                    var msgObj1 = ret.ret.order_arr;
                    var msgObj2 = ret.ret.pay_order_arr;
                    loadTwoBarChart('order_chart', msgObj1, msgObj2, '订单总数', '支付成功订单数', '笔', '笔');
                    $("#order_total_num").text(ret.ret.order_total_num);
                    $("#pay_order_total_num").text(ret.ret.pay_order_total_num);
                    $('#order_date_span').text(msgObj1[0]['date'] + '-' + msgObj1[msgObj1.length - 1]['date'])
                } else {

                }
            })
        }


    </script>
@endsection