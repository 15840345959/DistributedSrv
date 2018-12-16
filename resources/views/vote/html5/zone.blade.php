@extends('vote.html5.layouts.app')

@section('content')

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


    </style>

    <!--赛区列表-->
    <div style="background: #FFF;">
        <!--赛区-->
        @foreach($activities as $activity)
            <div class="aui-margin-l-15" style="border-bottom: 1px solid #f1f1f1;">
                <!--赛区积分信息-->
                <div class="aui-row aui-padded-t-15">
                    <div class="aui-col-xs-9">
                        <div>
                            <span class="aui-font-size-14">{{$activity->name}}</span>
                        </div>
                        <div class="aui-margin-t-10">
                            <span class="aui-font-size-14">时间 {{date_format(date_create($activity->apply_start_time),'Y/m/d')}}
                                -{{date_format(date_create($activity->vote_end_time),'m/d')}}</span>
                        </div>
                    </div>
                    <div class="aui-col-xs-3">
                        <span class="aui-btn aui-pull-right aui-margin-r-10 aui-margin-t-10 aui-font-size-12"
                              style="height: 28px;line-height: 28px;width: 70px;"
                              onclick="clickJoin('{{$activity->id}}');">参加</span>
                    </div>
                </div>
                <div class="aui-row aui-margin-t-15 aui-padded-b-15">
                    <div class="aui-col-xs-9">
                        <span class="aui-font-size-12 text-grey-999">参与人数</span>
                        <span class="aui-font-size-12 text-grey-999 aui-margin-l-10">{{$activity->join_num}}</span>
                        <span class="aui-font-size-12 text-grey-999 aui-margin-l-15">访问量</span>
                        <span class="aui-font-size-12 text-grey-999">{{$activity->show_num}}</span>
                    </div>
                    <div class="aui-col-xs-3">
                        <div class="aui-flex-col aui-flex-middle aui-pull-right">
                            <img src="{{URL::asset('/img/half_fire.png')}}" style="width: 16px;height: 16px;">
                            <span class="aui-margin-l-10 text-grey-999 aui-font-size-14 aui-margin-r-15">{{$activity->vote_num}}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div style="height: 60px;"></div>


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

    <script type="text/javascript">

        //微信相关/////////////////////////////////////////////////////
        wx.config({!! $wxConfig !!});

        //微信配置成功后
        wx.ready(function () {

        });

        ///////////////////////////////////////////////////////////////

        //点击参加大赛
        function clickJoin(activity_id) {
            toast_loading("参加大赛...")
            window.location.href = "{{URL::asset('/vote/index')}}?activity_id=" + activity_id;
        }

    </script>
@endsection