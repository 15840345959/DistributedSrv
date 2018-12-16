@extends('vote.html5.layouts.app')

@section('content')

    <link rel="stylesheet" href="{{asset('/dist/lib/minirefresh/minirefresh.min.css')}}">

    <style type="text/css">
        button, .aui-btn {
            color: #FFFFFF;
            background: #EA5858;
        }

        .aui-btn:active {
            color: #FFFFFF;
            background-color: #EA0000;
        }

        .aui-tab-item.aui-active {
            color: #000000;
            border-bottom: 2px solid #EA5858;
        }

        /*公告的渐变*/
        .gg-trans {
            filter: Alpha(Opacity=70); /* for IE */
            background-color: rgba(234, 88, 88, 0.7); /*for FF*/
        }

        ::-webkit-input-placeholder {
            /* WebKit browsers */
            color: #999;
        }

        :-moz-placeholder {
            /* Mozilla Firefox 4 to 18 */
            color: #999;
        }

        ::-moz-placeholder {
            /* Mozilla Firefox 19+ */
            color: #999;
        }

        :-ms-input-placeholder {
            /* Internet Explorer 10+ */
            color: #999;
        }

        /*video自适应手机*/
        .vid-wrap {
            width: 100%;
            background: #000;
            position: relative;
            padding-bottom: 56.25%; /*需要用padding来维持16:9比例,也就是9除以16*/
            height: 0;
        }

        .vid-wrap video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        /*.tab-item-content {*/
        /*display: none;*/
        /*}*/

        /*.active {*/
        /*display: block;*/
        /*}*/

        /*.aui-tab-item {*/
        /*height: 53px !important;*/
        /*}*/

        .minirefresh-wrap {
            top: 2.2rem !important;
        }
    </style>

    <title>团队详情</title>

    <div style="position: fixed;width: 100%;z-index: 99;">
        <div class="aui-tab" id="tab">
            <div class="aui-tab-item aui-active">进行中</div>
            <div class="aui-tab-item">未结算</div>
            <div class="aui-tab-item">已结算</div>
        </div>
    </div>

    {{--<div style="height: 2.7rem;"></div>--}}

    <div id="minirefresh-0" class="minirefresh-wrap">
        <div class="minirefresh-scroll">
            <div id="tab-item-content-0" class="tab-item-content">
                @if ($activity_count['ongoing'])
                    <ul class="aui-list aui-media-list">
                        @foreach($activity['ongoing'] as $item)
                            <li class="aui-list-item aui-list-item-middle">
                                <div class="aui-media-list-item-inner">
                                    <div class="aui-list-item-inner">
                                        <div class="aui-list-item-text">
                                            <div class="aui-list-item-title aui-font-size-14 aui-ellipsis-1">{{$item->name}}</div>
                                            <div class="aui-list-item-right">{{$item->code}}</div>
                                        </div>
                                        <div class="aui-list-item-text">
                                            {{--<div>--}}
                                            {{--<span>参与人数：</span><span>{{$item->vote_user_count}}</span>--}}
                                            {{--</div>--}}

                                            {{--<div>--}}
                                            {{--<span>礼物总额：</span><span>{{$item->gift_money}}</span>--}}
                                            {{--</div>--}}

                                            <div>
                                                <span>激活状态：</span>
                                                <span>
                                                    @if($item->valid_status == 0)
                                                        <div class="aui-label aui-label-warning">未激活</div>
                                                    @else
                                                        <div class="aui-label aui-label-success">已激活</div>
                                                    @endif
                                                </span>
                                            </div>

                                            @if ($item->gift_money < $team->amount ? $team->amount : 500)
                                                <div>
                                                    <span>礼物总额：</span><span
                                                            class="aui-label aui-label-info">{{$item->gift_money}}</span>
                                                </div>
                                            @endif

                                            <div>
                                                <span>参与人数：</span><span
                                                        class="aui-label aui-label-primary">{{$item->vote_user_count}}</span>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div style="padding-top: 60px;">
                        <img src="{{asset('/img/nodate_tip.png')}}" style="width: 60%;margin: auto;">
                    </div>
                @endif
            </div>
        </div>
    </div>


    <div id="minirefresh-1" class="minirefresh-wrap aui-hide">
        <div class="minirefresh-scroll">
            <div id="tab-item-content-1" class="tab-item-content aui-hide">
                @if ($activity_count['not_settle_activity'])
                    <ul class="aui-list aui-media-list">
                        @foreach($activity['not_settle_activity'] as $item)
                            <li class="aui-list-item aui-list-item-middle">
                                <div class="aui-media-list-item-inner">
                                    <div class="aui-list-item-inner">
                                        <div class="aui-list-item-text">
                                            <div class="aui-list-item-title aui-font-size-14 aui-ellipsis-1">{{$item->name}}</div>
                                            <div class="aui-list-item-right">{{$item->code}}</div>
                                        </div>
                                        <div class="aui-list-item-text">


                                            <div>
                                                <span>激活状态：</span>
                                                <span>
                                                    @if($item->valid_status == 0)
                                                        <div class="aui-label aui-label-warning">未激活</div>
                                                    @else
                                                        <div class="aui-label aui-label-success">已激活</div>
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div style="padding-top: 60px;">
                        <img src="{{asset('/img/nodate_tip.png')}}" style="width: 60%;margin: auto;">
                    </div>
                @endif
            </div>
        </div>
    </div>


    <div id="minirefresh-2" class="minirefresh-wrap aui-hide">
        <div class="minirefresh-scroll">
            <div id="tab-item-content-2" class="tab-item-content aui-hide">
                @if ($activity_count['settle_activity'])
                    <ul class="aui-list aui-media-list">
                        @foreach($activity['settle_activity'] as $item)
                            <li class="aui-list-item aui-list-item-middle">
                                <div class="aui-media-list-item-inner">
                                    <div class="aui-list-item-inner">
                                        <div class="aui-list-item-text">
                                            <div class="aui-list-item-title aui-font-size-14 aui-ellipsis-1">{{$item->name}}</div>
                                            <div class="aui-list-item-right">{{$item->code}}</div>
                                        </div>
                                        <div class="aui-list-item-text">
                                            {{--<div>--}}
                                            {{--<span>参与人数：</span><span>{{$item->vote_user_count}}</span>--}}
                                            {{--</div>--}}

                                            {{--<div>--}}
                                            {{--<span>礼物总额：</span><span>{{$item->gift_money}}</span>--}}
                                            {{--</div>--}}

                                            <div>
                                                <span>激活状态：</span>
                                                <span>
                                                    @if($item->valid_status == 0)
                                                        <div class="aui-label aui-label-warning">未激活</div>
                                                    @else
                                                        <div class="aui-label aui-label-success">已激活</div>
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div style="padding-top: 60px;">
                        <img src="{{asset('/img/nodate_tip.png')}}" style="width: 60%;margin: auto;">
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{--<div style="height: 60px;"></div>--}}
    <script id="activity-template" type="text/x-dot-template">
        <li class="aui-list-item aui-list-item-middle">
            <div class="aui-media-list-item-inner">
                <div class="aui-list-item-inner">
                    <div class="aui-list-item-text">
                        <div class="aui-list-item-title aui-font-size-14 aui-ellipsis-1">@{{= it.name }}</div>
                        <div class="aui-list-item-right">@{{= it.code }}</div>
                    </div>
                    <div class="aui-list-item-text">
                        {{--<div>--}}
                        {{--<span>参与人数：</span><span>{{$item->vote_user_count}}</span>--}}
                        {{--</div>--}}

                        {{--<div>--}}
                        {{--<span>礼物总额：</span><span>{{$item->gift_money}}</span>--}}
                        {{--</div>--}}

                        <div>
                            <span>激活状态：</span>
                            <span>
                                @{{? it.valid_status }}
                                    <div class="aui-label aui-label-warning">已激活</div>
                                @{{ ?? }}
                                    <div class="aui-label aui-label-success">未激活</div>
                                @{{?}}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </li>
    </script>

@endsection

@section('script')
    <script>
        var _mtac = {};
        (function () {
            var mta = document.createElement("script");
            mta.src = "http://pingjs.qq.com/h5/stats.js?v2.0.2";
            mta.setAttribute("name", "MTAH5");
            mta.setAttribute("sid", "500636629");
            var s = document.getElementsByTagName("script")[0];
            s.parentNode.insertBefore(mta, s);
        })();
    </script>

    <script type="text/javascript" src="{{ asset('/js/aui/aui-tab.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/dist/lib/minirefresh/minirefresh.min.js') }}"></script>
    {{--<script type="text/javascript" src="{{ asset('/js/doT.min.js') }}"></script>--}}



    <script type="text/javascript">
        wx.config({!! $wxConfig !!});

        var team_id = '{{$team_id}}'
        var page = 2
        var listDomArr = []
        var miniRefreshArr = []
        var requestDelayTime = 600

        var tab_index = 0

        var tab = new auiTab({
            element: document.getElementById("tab"),
            index: 1,
            repeatClick: false
        }, function (ret) {
            console.log(ret)

            $('.tab-item-content').addClass('aui-hide')
            $('.tab-item-content').eq(ret.index - 1).removeClass('aui-hide')

            $('.minirefresh-wrap').addClass('aui-hide')
            $('.minirefresh-wrap').eq(ret.index - 1).removeClass('aui-hide')

            tab_index = ret.index - 1

            if (!miniRefreshArr[tab_index]) {
                initMiniRefreshs(tab_index)
            }
        })

        var initMiniRefreshs = function (index) {
            listDomArr[index] = document.querySelector('#tab-item-content-' + index)

            miniRefreshArr[index] = new MiniRefresh({
                container: '#minirefresh-' + index,
                down: {
                    callback: function () {
                        // setTimeout(function() {
                        //     // 每次下拉刷新后，上拉的状态会被自动重置
                        //     // appendTestData(listDomArr[index], 10, true, index)
                        //     miniRefreshArr[index].endDownLoading(true)
                        //
                        // }, requestDelayTime)

                        // getTeamActivityByType(index)

                        console.log('down')
                        miniRefreshArr[index].endDownLoading(true)
                    }
                },
                up: {
                    isAuto: true,
                    callback: function () {
                        // setTimeout(function() {
                        //     // appendTestData(listDomArr[index], 10, false, index)
                        //     miniRefreshArr[index].endUpLoading(true)
                        // }, requestDelayTime)

                        miniRefreshArr[index].endUpLoading(true)
                        console.log('up')

                    }
                }
            })
        }

        initMiniRefreshs(0)

        function getTeamActivityByType(type) {
            ajaxRequest("{{route('vote.team.activity')}}", {
                team_id: team_id,
                activity_type: type + 1,
                page: page
            }, 'get', function (ret, err) {
                console.log('ret is : ', ret)
                console.log('err is : ', err)

                if (ret.ret.date) {
                    miniRefreshArr[type].endUpLoading(false)
                    for (var i in ret.ret.date) {
                        var interText = doT.template($("#activity-template").text())
                        $("#tab-item-content-" + type).append(interText(ret.ret.date[i]))
                    }
                } else {
                    miniRefreshArr[type].endUpLoading(true)
                }


            })
        }

    </script>


@endsection