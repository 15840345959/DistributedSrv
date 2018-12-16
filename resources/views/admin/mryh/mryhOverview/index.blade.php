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

    <nav class="breadcrumb">
        <i class="Hui-iconfont">&#xe67f;</i>
        首页
        <span class="c-gray en">&gt;</span>
        业务预览
        <a class="btn btn-success radius r btn-refresh" style="line-height:1.6em;margin-top:3px" title="刷新"
           onclick="location.replace('{{URL::asset('/admin/mryh/mryhOverview/index')}}');">
            <i class="Hui-iconfont">&#xe68f;</i>
        </a>
    </nav>

    <section class="content-body">
        <div class="content" style="padding: 2rem;padding-bottom: 0;">
            <div class="row">
                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3>{{$new_user_num}}/{{$total_user_num}}</h3>

                            <p>今日新增/累积用户（户）</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        {{--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>--}}
                    </div>
                </div>

                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3>{{$today_trans_money}}/{{$total_trans_moeny}}</h3>

                            <p>今日新增/累积交易（元）</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        {{--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>--}}
                    </div>
                </div>

                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3>{{$waiting_withdraw_money}}/{{$already_withdraw_money}}</h3>

                            <p>待提现/已提现金额（元）</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        {{--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>--}}
                    </div>
                </div>

                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3>{{$compute_task_num}}</h3>

                            <p>近3日清分场次（个）</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        {{--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>--}}
                    </div>
                </div>
            </div>


        </div>
        <div class="content" style="margin: 2rem;">

            <div class="panel panel-default">
                <div class="panel-header">用户增长趋势（<span id="user_date_span"></span>） 共计增长用户：<span
                            id="user_total_num"></span>户
                </div>
                <div class="panel-body">
                    <div class="btn-group" role="group" aria-label="...">
                        <button type="button" class="btn btn-default user-date-type active" onclick="clickUserData(7)">
                            近7天
                        </button>
                        <button type="button" class="btn btn-default user-date-type" onclick="clickUserData(14)">近14天
                        </button>
                        <button type="button" class="btn btn-default user-date-type" onclick="clickUserData(30)">近30天
                        </button>
                        <button type="button" class="btn btn-default user-date-type" onclick="clickUserData(60)">近60天
                        </button>
                        <button type="button" class="btn btn-default user-date-type" onclick="clickUserData(90)">近90天
                        </button>
                    </div>

                    <div id="user_chart" style="height: 300px;">

                    </div>
                </div>
            </div>
        </div>


        <div class="content" style="margin: 2rem;">

            <div class="panel panel-default">
                <div class="panel-header">提现及提现失败金额趋势（<span id="withdrawCash_failed_date_span"></span>） 共计提现记录：<span
                            id="withdrawCash_total_num"></span>笔 提现失败记录：<span
                            id="withdrawFailed_total_num"></span>笔
                </div>
                <div class="panel-body">
                    <div class="btn-group" role="group" aria-label="...">
                        <button type="button" class="btn btn-default withdraw-failed-date-type active"
                                onclick="clickWithdraw_Failded(7)">
                            近7天
                        </button>
                        <button type="button" class="btn btn-default withdraw-failed-date-type"
                                onclick="clickWithdraw_Failded(14)">近14天
                        </button>
                        <button type="button" class="btn btn-default withdraw-failed-date-type"
                                onclick="clickWithdraw_Failded(30)">近30天
                        </button>
                        <button type="button" class="btn btn-default withdraw-failed-date-type"
                                onclick="clickWithdraw_Failded(60)">近60天
                        </button>
                        <button type="button" class="btn btn-default withdraw-failed-date-type"
                                onclick="clickWithdraw_Failded(90)">近90天
                        </button>
                    </div>

                    <div id="withdrawCash_failed_chart" style="height: 300px;">

                    </div>
                </div>
            </div>
        </div>

        <div class="content" style="margin: 2rem;">

            <div class="panel panel-default">
                <div class="panel-header">平台押金及退款金额（<span id="new_refund_joinOrder_span"></span>） 共计押金记录：<span
                            id="new_joinOrders_total_num"></span>笔 退款记录：<span
                            id="refund_joinOrders_total_num"></span>笔
                </div>
                <div class="panel-body">
                    <div class="btn-group" role="group" aria-label="...">
                        <button type="button" class="btn btn-default new-refund-date-type active"
                                onclick="getNew_Refund_joinOrder(7)">
                            近7天
                        </button>
                        <button type="button" class="btn btn-default  new-refund-date-type"
                                onclick="getNew_Refund_joinOrder(14)">近14天
                        </button>
                        <button type="button" class="btn btn-default  new-refund-date-type"
                                onclick="getNew_Refund_joinOrder(30)">近30天
                        </button>
                        <button type="button" class="btn btn-default  new-refund-date-type"
                                onclick="getNew_Refund_joinOrder(60)">近60天
                        </button>
                        <button type="button" class="btn btn-default  new-refund-date-type"
                                onclick="getNew_Refund_joinOrder(90)">近90天
                        </button>
                    </div>

                    <div id="new_refund_joinOrder_chart" style="height: 300px;">

                    </div>
                </div>
            </div>
        </div>

        <div class="content" style="margin: 2rem;">

            <div class="panel panel-default">
                <div class="panel-header">参赛及作品增长趋势（<span id="join_article_date_span"></span>） 共计参赛记录：<span
                            id="join_total_num"></span>次 共计作品：<span
                            id="article_total_num"></span>个
                </div>
                <div class="panel-body">
                    <div class="btn-group" role="group" aria-label="...">
                        <button type="button" class="btn btn-default join-article-date-type active"
                                onclick="clickJoin_ArticleData(7)">
                            近7天
                        </button>
                        <button type="button" class="btn btn-default join-article-date-type"
                                onclick="clickJoin_ArticleData(14)">近14天
                        </button>
                        <button type="button" class="btn btn-default join-article-date-type"
                                onclick="clickJoin_ArticleData(30)">近30天
                        </button>
                        <button type="button" class="btn btn-default join-article-date-type"
                                onclick="clickJoin_ArticleData(60)">近60天
                        </button>
                        <button type="button" class="btn btn-default join-article-date-type"
                                onclick="clickJoin_ArticleData(90)">近90天
                        </button>
                    </div>

                    <div id="join_article_chart" style="height: 300px;">

                    </div>
                </div>
            </div>
        </div>

    </section>
@endsection

@section('script')

    <script type="text/javascript" src="{{asset('js/echarts.min.js')}}"></script>
    <script>

        //初始化
        $(document).ready(function () {
            //初始化数据
            clickUserData(7);
            clickJoin_ArticleData(7);
            clickWithdraw_Failded(7);
            clickNew_RefundJoinOrder(7);
        })

        //点击用户趋势信息
        function clickUserData(days_num) {
            $('.user-date-type').removeClass('active')
            $(this).addClass('active')
            getUser(days_num);
        }

        //点击参赛和作品趋势信息
        function clickJoin_ArticleData(days_num) {
            $('.join-article-date-type').removeClass('active')
            $(this).addClass('active')
            getJoin_Article(days_num);
        }

        //提现相关
        function clickWithdraw_Failded(days_num) {
            $('.withdraw-failed-date-type').removeClass('active')
            $(this).addClass('active')
            getWithDrawCash_Failed(days_num);
        }

        //平台押金及退款趋势
        function clickNew_RefundJoinOrder(days_num) {
            $('.new-refund-date-type').removeClass('active')
            $(this).addClass('active')
            getNew_Refund_joinOrder(days_num);
        }


        //获取用户增长趋势
        function getUser(days_num) {
            ajaxRequest('{{URL::asset('/admin/mryh/mryhOverview/user')}}', {"days_num": days_num}, "GET", function (ret) {
                if (ret.result === true) {
                    var msgObj = ret.ret.user_arr;
                    loadLineChart('user_chart', msgObj, "新增用户", "户");
                    $("#user_total_num").text(ret.ret.user_total_num);
                    $('#user_date_span').text(msgObj[0]['date'] + '-' + msgObj[msgObj.length - 1]['date'])
                } else {

                }
            })
        }

        //获取参赛及作品趋势
        function getJoin_Article(days_num) {
            ajaxRequest('{{URL::asset('/admin/mryh/mryhOverview/join_article')}}', {"days_num": days_num}, "GET", function (ret) {
                if (ret.result === true) {
                    var msgObj1 = ret.ret.join_arr;
                    var msgObj2 = ret.ret.article_arr;
                    loadTwoLineChart('join_article_chart', msgObj1, msgObj2, '新增参赛', '新增作品', '次', '个');
                    $("#join_total_num").text(ret.ret.join_total_num);
                    $("#article_total_num").text(ret.ret.article_total_num);
                    $('#join_article_date_span').text(msgObj1[0]['date'] + '-' + msgObj1[msgObj1.length - 1]['date'])
                } else {

                }
            })
        }

        //获取提现及提现失败金额表
        function getWithDrawCash_Failed(days_num) {
            ajaxRequest('{{URL::asset('/admin/mryh/mryhOverview/withdraw_failed')}}', {"days_num": days_num}, "GET", function (ret) {
                if (ret.result === true) {
                    var msgObj1 = ret.ret.withdrawCash_arr;
                    var msgObj2 = ret.ret.withdrawFailed_arr;
                    loadTwoBarChart('withdrawCash_failed_chart', msgObj1, msgObj2, '提现成功金额', '提现失败金额', '元', '元');
                    $("#withdrawCash_total_num").text(ret.ret.withdrawCash_total_num);
                    $("#withdrawFailed_total_num").text(ret.ret.withdrawFailed_total_num);
                    $('#withdrawCash_failed_date_span').text(msgObj1[0]['date'] + '-' + msgObj1[msgObj1.length - 1]['date'])
                } else {

                }
            })
        }

        //获取订单及退款
        function getNew_Refund_joinOrder(days_num) {
            ajaxRequest('{{URL::asset('/admin/mryh/mryhOverview/new_refund_joinOrder')}}', {"days_num": days_num}, "GET", function (ret) {
                if (ret.result === true) {
                    var msgObj1 = ret.ret.new_joinOrders_arr;
                    var msgObj2 = ret.ret.refund_joinOrders_arr;
                    loadTwoBarChart('new_refund_joinOrder_chart', msgObj1, msgObj2, '平台押金金额', '成功退款金额', '元', '元');
                    $("#new_joinOrders_total_num").text(ret.ret.new_joinOrders_total_num);
                    $("#refund_joinOrders_total_num").text(ret.ret.refund_joinOrders_total_num);
                    $('#new_refund_joinOrder_span').text(msgObj1[0]['date'] + '-' + msgObj1[msgObj1.length - 1]['date'])
                } else {

                }
            })
        }


    </script>
@endsection