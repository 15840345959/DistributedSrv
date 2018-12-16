@extends('admin.layouts.app')

@section('style')
    <link rel="stylesheet" href="">
@endsection

@section('content')

    <nav class="breadcrumb">
        <i class="Hui-iconfont">&#xe67f;</i>
        首页
        <span class="c-gray en">&gt;</span>
        业务预览
        <a class="btn btn-success radius r btn-refresh" style="line-height:1.6em;margin-top:3px" title="刷新"
           onclick="location.replace('{{route('overview.index')}}');">
            <i class="Hui-iconfont">&#xe68f;</i>
        </a>
    </nav>

    <section class="content-body">
        <div class="content" style="padding: 2rem;padding-bottom: 0;">
            <div class="row">
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

                <div class="col-lg-3 col-xs-6">
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3>{{$today_total_fee}}</h3>

                            <p>今日收入</p>
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
                            <h3>{{$today_activity_count}}</h3>

                            <p>今日新增场次</p>
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
                            <h3>{{$wait_audit}}</h3>

                            <p>等待审核人数</p>
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
                            <h3>暂无</h3>

                            <p>今日流量</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        {{--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>--}}
                    </div>
                </div>

            </div>

        </div>

        <div class="content" style="padding: 2rem;padding-top: 0;padding-bottom: 0">
            <div class="row">

                <div class="col-lg-6 col-xs-12">
                    <div class="panel panel-default">
                        <div class="panel-header">各场次状态</div>
                        <div class="panel-body">
                            <div id="activity_status_charts" style="height: 300px;">

                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-xs-12">
                    <div class="panel panel-default">
                        <div class="panel-header">场次警告 共{{$activity->count()}}场</div>
                        <div class="panel-body">
                            <div id="activity_status_charts" style="height: 300px;overflow-y: scroll;">

                                <div>
                                    <ul class="products-list product-list-in-box" style="text-indent: 0 !important;">
                                        @foreach($activity as $item)
                                            @if ($item->vote_user_count < 30 || $item->valid_status == 0)
                                                <li class="item">
                                                    <div class="product-info">
                                                        @if($item->vote_user_count < 30)
                                                            <span class="label label-danger pull-right">
                                                                未上传
                                                            </span>
                                                        @elseif($item->valid_status == 0)
                                                            <span class="label label-warning pull-right">
                                                                未激活
                                                            </span>
                                                        @endif

                                                        <a class="product-description" style="color: #333 !important;"
                                                           onclick="creatIframe('{{route('voteActivity.index', ['search_word' => $item->name])}}', '{{$item->name}}')">
                                                            {{$item->name}} @if(isset($item->code)) <span
                                                                    style="color: #999 !important;">({{$item->code}}
                                                                )</span> @endif
                                                        </a>
                                                    </div>
                                                </li>

                                            @endif
                                        @endforeach
                                    </ul>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="content" style="margin: 2rem;">

            <div class="panel panel-default">
                <div class="panel-header">收入与投票趋势（<span id="income_vote_date"></span>）共计收入：<span
                            id="income_total"></span>元 共计投票：<span id="vote_total"></span>票
                </div>
                <div class="panel-body">
                    <div class="btn-group" role="group" aria-label="...">
                        <button type="button" class="btn btn-default income-date-type active" data-type="week">近7天
                        </button>
                        <button type="button" class="btn btn-default income-date-type" data-type="half_month">近14天
                        </button>
                        <button type="button" class="btn btn-default income-date-type" data-type="month">近30天</button>
                    </div>

                    <div id="income_charts" style="height: 300px;">

                    </div>
                </div>
            </div>
        </div>

        <div class="content" style="margin: 2rem;">

            <div class="panel panel-default">
                <div class="panel-header">订单趋势图（<span id="order_date"></span>）总订单：<span id="all_order_total"></span>笔
                    已支付订单：<span id="pay_order_total"></span>笔
                </div>
                <div class="panel-body">
                    <div class="btn-group" role="group" aria-label="...">
                        <button type="button" class="btn btn-default order-date-type active" data-type="week">近7天
                        </button>
                        <button type="button" class="btn btn-default order-date-type" data-type="half_month">近14天
                        </button>
                        <button type="button" class="btn btn-default order-date-type" data-type="month">近30天</button>
                    </div>

                    <div id="order_charts" style="height: 300px;">

                    </div>
                </div>
            </div>
        </div>

        <div class="content" style="margin: 2rem;">

            <div class="panel panel-default">
                <div class="panel-header">新增场次趋势（<span id="new_activity_date"></span>）共计场次：<span
                            id="new_activity_total"></span>场
                </div>
                <div class="panel-body">
                    <div class="btn-group" role="group" aria-label="...">
                        <button type="button" class="btn btn-default new-activity-date-type active" data-type="week">
                            近7天
                        </button>
                        <button type="button" class="btn btn-default new-activity-date-type" data-type="half_month">
                            近14天
                        </button>
                        <button type="button" class="btn btn-default new-activity-date-type" data-type="month">近30天
                        </button>
                    </div>

                    <div id="new_activity_charts" style="height: 300px;">

                    </div>
                </div>
            </div>
        </div>


        {{--<div class="content" style="margin: 2rem;">--}}

        {{--<div class="panel panel-default">--}}
        {{--<div class="panel-header">投票趋势图（<span id="vote_date"></span>）共计票数：<span id="vote_total"></span>票</div>--}}
        {{--<div class="panel-body">--}}
        {{--<div class="btn-group" role="group" aria-label="...">--}}
        {{--<button type="button" class="btn btn-default vote-date-type active" data-type="week">近7天</button>--}}
        {{--<button type="button" class="btn btn-default vote-date-type" data-type="half_month">近14天</button>--}}
        {{--<button type="button" class="btn btn-default vote-date-type" data-type="month">近30天</button>--}}
        {{--</div>--}}

        {{--<div id="vote_charts" style="height: 300px;">--}}

        {{--</div>--}}
        {{--</div>--}}
        {{--</div>--}}
        {{--</div>--}}
    </section>
@endsection

@section('script')

    <script type="text/javascript" src="{{asset('js/echarts.min.js')}}"></script>
    <script>
        var activity_status_charts
        var income_charts
        var new_activity_charts
        var order_charts
        var vote_charts

        $(document).ready(function () {
            ajaxRequest('{{ route('overview.activityStatus') }}', {}, "GET", function (ret) {
                if (ret.result === true) {
                    loadActivityStatusCharts(ret.ret)
                } else {

                }
            })

            getIncome('week')
            getNewActivity('week')
            getOrder('week')
            // getVote('week')
        })

        function loadActivityStatusCharts(data) {
            console.log(data)

            activity_status_charts = echarts.init(document.getElementById('activity_status_charts'))

            var setOption = {
                tooltip: {
                    trigger: 'item',
                    formatter: "{a} <br/>{b} : {c} 场 ({d}%)"
                },
                legend: {
                    type: 'scroll',
                    orient: 'vertical',
                    right: '20%',
                    top: 'middle',
                    data: ['待上传', '待激活', '已结束', '正常'],
                    textStyle: {
                        fontSize: 24
                    },
                    formatter: function (name) {
                        var total = 0;
                        var target;
                        for (var i = 0, l = data.length; i < l; i++) {
                            total += data[i].value;
                            if (data[i].name == name) {
                                target = data[i].value
                            }
                        }
                        // return name + ' ' + ((target/total)*100).toFixed(2) + '%';
                        return name + '：  ' + target + ' 场';
                    }
                },
                series: [
                    {
                        name: '场次状态',
                        type: 'pie',
                        radius: '60%',
                        center: ['30%', '50%'],
                        data: data,
                        itemStyle: {
                            emphasis: {
                                shadowBlur: 10,
                                shadowOffsetX: 0,
                                shadowColor: 'rgba(0, 0, 0, 0.5)'
                            }
                        }
                    }
                ]
            }

            activity_status_charts.setOption(setOption)

            activity_status_charts.hideLoading()
        }

        function getIncome(type) {
            ajaxRequest('{{ route('overview.income') }}', {type: type}, "GET", function (ret) {
                console.log('getIncome ret is : ', JSON.stringify(ret))
                if (ret.result === true) {
                    loadIncomeCharts(ret.ret['income'], ret.ret['vote'])
                    $('#income_total').text(ret.ret['income_total'].toFixed(2))
                    $('#vote_total').text(ret.ret['vote_total'] ? ret.ret['vote_total'] : 0)
                    $('#income_vote_date').text(ret.ret['income'][0]['date'] + '-' + ret.ret['income'][ret.ret['income'].length - 1]['date'])
                } else {

                }
            })
        }

        function getNewActivity(type) {
            ajaxRequest('{{ route('overview.newActivity') }}', {type: type}, "GET", function (ret) {
                console.log('getNewActivity ret is : ', JSON.stringify(ret))
                if (ret.result === true) {
                    loadNewActivityCharts(ret.ret['activity'])
                    $('#new_activity_total').text(ret.ret['total'])
                    $('#new_activity_date').text(ret.ret['activity'][0]['date'] + '-' + ret.ret['activity'][ret.ret['activity'].length - 1]['date'])
                } else {

                }
            })
        }

        function getOrder(type) {
            ajaxRequest('{{ route('overview.order') }}', {type: type}, "GET", function (ret) {
                console.log('getOrder ret is : ', JSON.stringify(ret))
                if (ret.result === true) {
                    loadOrderCharts(ret.ret['order'])
                    $('#all_order_total').text(ret.ret['total'][0]['all_count'])
                    $('#pay_order_total').text(ret.ret['total'][0]['pay_count'])
                    $('#order_date').text(ret.ret['order'][0]['date'] + '-' + ret.ret['order'][ret.ret['order'].length - 1]['date'])
                } else {

                }
            })
        }

        function getVote(type) {
            ajaxRequest('{{ route('overview.vote') }}', {type: type}, "GET", function (ret) {
                console.log('getVote ret is : ', JSON.stringify(ret))
                if (ret.result === true) {
                    loadVoteCharts(ret.ret['vote'])
                    $('#vote_total').text(ret.ret['total'])
                    $('#vote_date').text(ret.ret['vote'][0]['date'] + '-' + ret.ret['vote'][ret.ret['vote'].length - 1]['date'])
                } else {

                }
            })
        }

        function loadIncomeCharts(income, vote) {
            // console.log(income)

            income_charts = echarts.init(document.getElementById('income_charts'))

            income_charts.showLoading({
                type: 'default'
            })

            var date_array = []
            var income_array = []
            var vote_array = []

            for (var i = 0; i < income.length; i++) {
                date_array.push(income[i].date)
                income_array.push(income[i].count)
                vote_array.push(vote[i].count)
            }

            var setOption = {
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'cross',
                        crossStyle: {
                            color: '#999'
                        }
                    }
                },
                legend: {
                    data: ['收入', '投票']
                },
                xAxis: [
                    {
                        type: 'category',
                        data: date_array,
                        axisPointer: {
                            type: 'shadow'
                        }
                    }
                ],
                yAxis: [
                    {
                        type: 'value',
                        name: '收入',
                        axisLabel: {
                            formatter: '{value} 元'
                        },
                        minInterval: 1
                    },
                    {
                        type: 'value',
                        name: '投票',
                        axisLabel: {
                            formatter: '{value} 票'
                        },
                        minInterval: 1
                    }
                ],
                series: [
                    {
                        name: '收入',
                        type: 'line',
                        data: income_array
                    },
                    {
                        name: '投票',
                        type: 'line',
                        data: vote_array,
                        yAxisIndex: 1
                    },
                ]
            }

            income_charts.setOption(setOption)

            income_charts.hideLoading()
        }

        function loadNewActivityCharts(data) {
            console.log(data)

            new_activity_charts = echarts.init(document.getElementById('new_activity_charts'))

            new_activity_charts.showLoading({
                type: 'default'
            })

            var date_array = []
            var data_array = []

            for (var i = 0; i < data.length; i++) {
                date_array.push(data[i].date)
                data_array.push(data[i].count)
            }

            var setOption = {
                tooltip: {
                    trigger: 'axis',
                    formatter: "{b} : {c} 场"
                },
                xAxis: {
                    type: 'category',
                    data: date_array
                },
                yAxis: {
                    type: 'value',
                    axisLabel: {
                        formatter: '{value} 场'
                    }
                },
                series: [{
                    data: data_array,
                    type: 'line'
                }]
            }

            new_activity_charts.setOption(setOption)

            new_activity_charts.hideLoading()
        }

        function loadOrderCharts(data) {
            console.log(data)

            order_charts = echarts.init(document.getElementById('order_charts'))

            order_charts.showLoading({
                type: 'default'
            })

            var date_array = []
            var all_order_array = []
            var pay_order_array = []

            for (var i = 0; i < data.length; i++) {
                date_array.push(data[i].date)
                all_order_array.push(data[i].all_order)
                pay_order_array.push(data[i].pay_order)
            }

            var setOption = {
                tooltip: {
                    trigger: 'axis',
                    // formatter: "{b} : {c} 笔"
                },
                legend: {
                    data: ['总订单数', '已支付订单数']
                },
                xAxis: {
                    type: 'category',
                    data: date_array
                },
                yAxis: [
                    {
                        type: 'value',
                        axisLabel: {
                            formatter: '{value} 笔'
                        }
                    }, {
                        type: 'value',
                        axisLabel: {
                            formatter: '{value} 笔'
                        }
                    }
                ],
                series: [{
                    name: '总订单数',
                    data: all_order_array,
                    type: 'bar'
                }, {
                    name: '已支付订单数',
                    data: pay_order_array,
                    type: 'bar'
                }
                ]
            }

            order_charts.setOption(setOption)

            order_charts.hideLoading()
        }

        function loadVoteCharts(data) {
            console.log(data)

            vote_charts = echarts.init(document.getElementById('vote_charts'))

            vote_charts.showLoading({
                type: 'default'
            })

            var date_array = []
            var data_array = []

            for (var i = 0; i < data.length; i++) {
                date_array.push(data[i].date)
                data_array.push(data[i].count)
            }

            var setOption = {
                tooltip: {
                    trigger: 'axis',
                    formatter: "{b} : {c} 票"
                },
                xAxis: {
                    type: 'category',
                    data: date_array
                },
                yAxis: {
                    type: 'value',
                    axisLabel: {
                        formatter: '{value} 票'
                    }
                },
                series: [{
                    data: data_array,
                    type: 'line'
                }]
            }

            vote_charts.setOption(setOption)

            vote_charts.hideLoading()
        }

        $('.income-date-type').on('click', function () {
            income_charts.showLoading({
                type: 'default'
            })

            getIncome($(this).attr('data-type'))

            $('.income-date-type').removeClass('active')

            $(this).addClass('active')

        })

        $('.new-activity-date-type').on('click', function () {
            new_activity_charts.showLoading({
                type: 'default'
            })

            getNewActivity($(this).attr('data-type'))

            $('.new-activity-date-type').removeClass('active')

            $(this).addClass('active')

        })

        $('.order-date-type').on('click', function () {
            order_charts.showLoading({
                type: 'default'
            })

            getOrder($(this).attr('data-type'))

            $('.order-date-type').removeClass('active')

            $(this).addClass('active')

        })

        $('.vote-date-type').on('click', function () {
            vote_charts.showLoading({
                type: 'default'
            })

            getVote($(this).attr('data-type'))

            $('.vote-date-type').removeClass('active')

            $(this).addClass('active')
        })
    </script>
@endsection