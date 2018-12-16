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

        /*提示蒙版*/
        .tip_div {
            width: 100%;
            height: 100%;
            position: fixed;
            left: 0px;
            top: 0px;
        }

        .mask_div {
            width: 100%;
            height: 100%;
            background-color: #000;
            filter: alpha(opacity=65);
            -moz-opacity: 0.65;
            opacity: 0.65;
            /*position: fixed;*/
            /*left: 0px;*/
            /*top: 0px;*/
        }

        /*选中gift对象的样式*/
        .sel-gift {
            background: #f1f1f1;
            border: 1px solid #FF5959;
            border-radius: 5px;
        }

        .no-sel-gift {
            background: #FFFFFF;
            border: 1px solid #FFFFFF;
            border-radius: 5px;
        }

        video {
            max-width: 100%;
        }

    </style>

    <title>我是{{$vote_user->name}}，编号{{$vote_user->code}},正在参加{{$activity->name}}</title>

    <!--轮播+公告-->
    <div style="position: relative;">
    @if($activity->notice_text)
        <!--公告部分-->
            <div style="width: 100%;z-index: 99;position: absolute;top: 0rem;height: 36px;"
                 class="main-bg gg-trans">
                <div class="aui-row" style="padding-top: 7px;">
                    <div class="aui-col-xs-1">
                        <img src="../img/laba.png" class="aui-margin-l-10"
                             style="width: 20px;height: 20px;">
                    </div>
                    <div class="aui-col-xs-11">
                    <span class="aui-margin-l-10 text-oneline" style="width: 90%;"
                          onclick="clickNotice('{{$activity->notice_url}}')">
                        <marquee direction="left"
                                 onmouseover="this.stop()" onmouseout="this.start()">
                                <a href="" class="aui-text-white">{{$activity->notice_text}}</a>
                        </marquee>
                </span>
                    </div>
                </div>
            </div>
    @endif

    <!--轮播图-->
        <div id="aui-slide">
            <div class="aui-slide-wrap">
                @foreach($index_ads as $index_ad)
                    <div class="aui-slide-node bg-dark">
                        <img src="{{$index_ad['img']}}?imageView2/1/w/600/h/350/interlace/1/q/75"/>
                    </div>
                @endforeach
            </div>
            <div class="aui-slide-page-wrap" style="z-index: 9;"><!--分页容器--></div>
        </div>
    </div>

    <!--用户信息-->
    <!--头部卡片-->
    <div style="background: white;">
        <div class="aui-row aui-padded-t-15 aui-padded-b-15">
            <div class="aui-col-xs-2">
                {{--用户头像的处理--}}
                {{--@if($vote_user->user)--}}
                {{--<img src="{{$vote_user->user->avatar}}" style="width:52px !important;height: 52px !important;"--}}
                {{--class="aui-img-round aui-margin-l-15">--}}
                {{--@else--}}
                {{--<img src="{{$vote_user->img_arr[0]}}{{strpos($vote_user->img_arr[0],'?')==false?'?imageView2/1/w/300/h/300/interlace/1/q/75':'/w/300/h/300'}}"--}}
                {{--style="width:52px !important;height: 52px !important;"--}}
                {{--class="aui-img-round aui-margin-l-15">--}}
                {{--@endif--}}

                {{--2018.11.21 阿伟提出头像默认为上传的图片--}}
                <img src="{{$vote_user->img_arr[0]}}{{strpos($vote_user->img_arr[0],'?')==false?'?imageView2/1/w/300/h/300/interlace/1/q/75':'/w/300/h/300'}}"
                     style="width:52px !important;height: 52px !important;"
                     class="aui-img-round aui-margin-l-15">
            </div>
            <div class="aui-col-xs-10">
                <div class="aui-margin-l-15 aui-row">
                    <div class="aui-col-xs-8">
                        <span class="aui-margin-l-15" style="line-height: 30px;">{{$vote_user->name}}</span>
                    </div>
                    <div class="aui-col-xs-4">

                        @if($vote_user->guanzhu_flag == true)
                            <div style="border: 1px solid #333333;width: 72px;line-height: 30px;border-radius: 15px;"
                                 class="aui-text-center text-grey-333 aui-font-size-12 aui-pull-right aui-margin-r-15">
                                已关注
                            </div>
                        @else
                            <div id="guanzhu_div"
                                 style="border: 1px solid #333333;width: 72px;line-height: 30px;border-radius: 15px;"
                                 class="aui-text-center text-grey-333 aui-font-size-12 aui-pull-right aui-margin-r-15"
                                 onclick="clickGuanZhu('{{$user->id}}','{{$vote_user->id}}');">+
                                关注
                            </div>
                        @endif
                    </div>
                </div>
                <div class="aui-margin-l-15 aui-margin-t-10">
                    @if($vote_user->declaration)
                        <div class="text-grey-999 aui-font-size-14 text-fourline aui-margin-l-10 aui-margin-r-10">
                            {{$vote_user->declaration}}
                        </div>
                    @else
                        <div class="text-grey-999 aui-font-size-14 text-fourline aui-margin-l-10 aui-margin-r-10">
                            <span>共有{{count($vote_user->img_arr)}} 幅作品</span>
                            <span class="aui-margin-l-10">{{$vote_user->fans_num}}个粉丝</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="aui-row aui-text-center aui-padded-t-15 aui-padded-b-15">
            @if(($activity->vote_status=='2') && $activity->valid_status=='1')
                <div class="aui-col-xs-4" style="border-right: 1px solid #EEEEEE;">
                    <div class="aui-font-size-18 main-color">
                        {{$vote_user->vote_num}}
                    </div>
                    <div class="aui-font-size-12 text-grey-999 aui-margin-t-5">
                        当前热度
                    </div>
                </div>
                <div class="aui-col-xs-4" style="border-right: 1px solid #EEEEEE;">
                    <div class="aui-font-size-18 main-color">
                        {{$activity->code}}-{{$vote_user->code}}
                    </div>
                    <div class="aui-font-size-12 text-grey-999 aui-margin-t-5">
                        编号
                    </div>
                </div>
                <div class="aui-col-xs-4" style="border-right: 1px solid #EEEEEE;">
                    <div class="aui-font-size-18  main-color">
                        @if($vote_user->less_vote_num==0)
                            第一名
                        @else
                            {{$vote_user->less_vote_num}}℃
                        @endif
                    </div>
                    <div class="aui-font-size-12 text-grey-999 aui-margin-t-5">
                        距第{{$vote_user->pm}}名还差
                    </div>
                </div>
            @else
                <div class="aui-col-xs-3" style="border-right: 1px solid #EEEEEE;">
                    <div class="aui-font-size-18 main-color">
                        {{$vote_user->gift_money}}
                    </div>
                    <div class="aui-font-size-12 text-grey-999 aui-margin-t-5">
                        礼物数
                    </div>
                </div>
                <div class="aui-col-xs-3" style="border-right: 1px solid #EEEEEE;">
                    <div class="aui-font-size-18 main-color">
                        {{$vote_user->vote_num}}
                    </div>
                    <div class="aui-font-size-12 text-grey-999 aui-margin-t-5">
                        当前热度
                    </div>
                </div>
                <div class="aui-col-xs-3" style="border-right: 1px solid #EEEEEE;">
                    <div class="aui-font-size-18 main-color">
                        {{$activity->code}}-{{$vote_user->code}}
                    </div>
                    <div class="aui-font-size-12 text-grey-999 aui-margin-t-5">
                        编号
                    </div>
                </div>
                <div class="aui-col-xs-3" style="border-right: 1px solid #EEEEEE;">
                    <div class="aui-font-size-18  main-color">
                        @if($vote_user->less_vote_num==0)
                            第一名
                        @else
                            {{$vote_user->less_vote_num}}℃
                        @endif
                    </div>
                    <div class="aui-font-size-12 text-grey-999 aui-margin-t-5">
                        距第{{$vote_user->pm}}名还差
                    </div>
                </div>
            @endif
        </div>

        {{--下载证书按钮--}}
        <div class="aui-text-center aui-margin-t-10 aui-padded-b-15">
            <img src="{{URL::asset('/img/get_cert_btn.jpg')}}" style="width: 50%;margin: auto;border-radius: 3px;"
                 onclick="clickSendCert({{$vote_user->id}},{{$vote_user->activity_id}});">
        </div>

    </div>

    <!--作品详情-->
    <div class="aui-margin-t-10" style="background: white;">
        @if($vote_user->work_name)
            <div class="aui-padded-15 font-size-16">
                {{$vote_user->work_name}}
            </div>
        @endif
        @if($vote_user->work_desc)
            <div class="aui-padded-15 font-size-14 text-grey-999">
                {{$vote_user->work_desc}}
            </div>
        @endif
        <div style="height: 10px;"></div>
        @if(\App\Components\Utils::isObjNull($vote_user->video)==false)
            <video id="video_video" src="{{$vote_user->video}}" controls="controls" style="width: 100%;"
                   class="aui-padded-10" autoplay="autoplay">
            </video>
        @endif
        <div style="height: 10px;"></div>
        @foreach($vote_user->img_arr as $img)
            <div class="aui-margin-b-10">
                <img src="{{$img}}{{strpos($img,'?')==false?'?imageView2/2/w/600/interlace/1/q/75':'/w/300/h/400'}}"
                     style="width: 100%;"
                     class="aui-padded-l-5 aui-padded-r-5">
            </div>
        @endforeach
        <div class="aui-padded-b-10 aui-text-center" onclick="clickComplain('{{$vote_user->id}}','{{$activity->id}}');">
            <span class="main-color aui-font-size-14">举报</span>
        </div>

    </div>

    {{--如果没有审核通过，则进行提醒--}}
    @if($vote_user->audit_status=='0')
        <div class="aui-margin-t-10" style="background: white;">
            <div class="aui-text-center"
                 style="background: white;border-bottom-left-radius: 5px;border-bottom-right-radius: 5px;">
                <div style="padding-top: 30px;">
                    <img src="{{URL::asset('/img/isart_fwh.jpg')}}" style="width: 150px;height: 150px;margin: auto;">
                </div>
                <div style="padding-top: 10px;">
                    <span class="aui-font-size-14" style="color: #FF5959;">关注公众号接受审核通知</span>
                </div>
                <div style="padding-top: 10px;padding-bottom: 30px;">
                    <span class="aui-font-size-14 aui-margin-l-10 aui-margin-r-10" style="color: #FF5959;">
                        参赛审核中，现可分享投票，审核通过后将在首页与排名页显示，本页面链接不变，祝您获奖。
                    </span>
                </div>
            </div>
        </div>
    @endif

    <!--贡献礼物列表-->
    @if($orders->count()>0)
        <div class="aui-margin-t-10" style="background: white;">
            <div class=" aui-margin-l-15 aui-font-size-14 aui-padded-t-10 aui-margin-b-10">助力排名</div>
            @foreach($group_by_user_orders as $group_by_user_order)
                <div style="width: 92%;background: #f1f1f1;border-radius: 5px;margin: auto;" class="aui-margin-t-10">
                    <div class="aui-flex-col aui-flex-middle aui-padded-t-10 aui-padded-b-10">
                        <span class="aui-margin-l-15">{{$group_by_user_order->pm}}</span>

                        <div style="position: relative;" class="aui-margin-l-10">
                            @if($group_by_user_order->pm==1)
                                <img src="../img/first_reward_icon.png" style="width: 74px;height: 80px;z-index: 99;">
                            @endif
                            @if($group_by_user_order->pm==2)
                                <img src="../img/second_reward_icon.png" style="width: 74px;height: 80px;z-index: 99;">
                            @endif
                            @if($group_by_user_order->pm==3)
                                <img src="../img/third_reward_icon.png" style="width: 74px;height: 80px;z-index: 99;">
                            @endif
                            <img src="{{$group_by_user_order->user->avatar}}"
                                 style="width: 46px;height: 46px;margin-top: -58px;margin-left: 18px;border-radius: 50%;">
                        </div>
                        <div class="aui-margin-l-15">
                            <div class="aui-font-size-14">{{$group_by_user_order->user->nick_name}}</div>
                            <div class="aui-margin-t-5 aui-font-size-14 text-grey-999">为小选手贡献了<span
                                        class="main-color">{{$group_by_user_order->total_vote_num}}℃</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="aui-text-center text-grey-999 aui-font-size-14 aui-padded-t-15 aui-margin-b-5">助力详情</div>
            @foreach($orders as $order)
                <ul class="aui-list aui-media-list aui-margin-l-10 aui-list-noborder aui-margin-t-5 order-list-div {{$order->show_flag=='true'?'':'aui-hide'}}">
                    <li class="aui-list-item aui-list-item-middle">
                        <div class="aui-media-list-item-inner">
                            <div class="aui-list-item-media" style="width: 3rem;">
                                <img src="{{$order->user->avatar}}" class="aui-img-round aui-list-img-sm">
                            </div>
                            <div class="aui-list-item-inner">
                                <div class="aui-list-item-text">
                                    <div class="aui-list-item-title aui-font-size-14">{{$order->user->nick_name}}
                                        送了{{$order->gift_num}}个{{$order->gift->name}}</div>
                                    <div class="aui-list-item-right">
                                        <img src="{{$order->gift->img}}" style="width: 24px;height: 24px;">
                                    </div>
                                </div>
                                <div class="aui-list-item-text aui-margin-t-5">
                                    {{$order->pay_at}}
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            @endforeach
            @if($orders->count()>6)
                <div id="show-more-div" class="aui-padded-t-10 aui-padded-b-15 aui-text-center">
                    <div style="border: 1px solid #EA5858;width: 72px;line-height: 30px;border-radius: 5px;display: inline-block;"
                         class="aui-text-center main-color aui-font-size-12 aui-margin-r-15" onclick="clickMore();">查看更多
                    </div>
                </div>
            @endif

        </div>
    @endif


    {{--获奖规则介绍--}}
    @if($activity->present_rule_html)
        <div class="aui-margin-t-10" style="background: #FFFFFF;">
            <div class="aui-padded-t-10 aui-text-center">
                <span class="aui-font-size-16">获奖规则介绍</span>
            </div>
            <div class="aui-text-center">
                <div class="aui-padded-15">
                    {!! $activity->present_rule_html !!}
                </div>
            </div>
        </div>
    @endif


    <!--投票成功的提示-->
    <div id="vote_success_div" class="tip_div aui-hide" style="z-index: 999;">
        <!--遮罩层-->
        <div class="mask_div"></div>
        <!--提示部分-->
        <div style="position: absolute;top: 120px;width: 100%;">
            <div style="width: 70%;margin: auto;">
                <div class="aui-text-center"
                     style="background: #FF5959;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <div>
                        <img src="{{URL::asset('/img/close_white_btn.png')}}" class="aui-padded-l-10 aui-padded-t-10"
                             style="width: 30px;height: 30px;" onclick="closeVoteSuccessTip();">
                    </div>
                    <div style="height: 10px;"></div>
                    <div>
                    <span style="margin-top: 30px;"
                          class="aui-font-size-14 aui-text-white">感谢支持，助我夺冠</span>
                    </div>
                    <div class="aui-padded-t-15">
                    <span id="latest_vote_num_span" style="margin-top: 30px;font-weight: bolder;font-size: 24px;"
                          class="aui-text-white">2283</span>
                        <span class="aui-font-size-14 aui-text-white">℃</span>
                    </div>
                    <div style="height: 30px;"></div>
                </div>
                <div class="aui-text-center"
                     style="background: white;border-bottom-left-radius: 5px;border-bottom-right-radius: 5px;">
                    <div style="padding-top: 10px;">
                        <img src="{{$tp_ad['img']}}?imageView2/1/w/600/h/350/interlace/1/q/75"
                             style="width: 94%;height: 160px;margin: auto;">
                    </div>
                    <div style="padding-top: 20px;padding-bottom: 20px;">
                        <div class="aui-row">
                            <div class="aui-col-xs-6 aui-text-center">
                            <span style="background: #f0f0f0;padding: 24px;height: 32px;line-height: 32px;border-radius: 5px;"
                                  class="aui-font-size-14 main-color" onclick="clickApply({{$activity->id}})">
                                报名参赛
                            </span>
                            </div>
                            <div class="aui-col-xs-6 aui-text-center">
                            <span style="padding: 24px;height: 32px;line-height: 32px;border-radius: 5px;"
                                  class="aui-font-size-14 main-bg aui-text-white" onclick="clickReward();">
                                助力选手
                            </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--没有投票机会提示-->
    <div id="vote_outof_num_div" class="tip_div aui-hide" style="z-index: 999;">
        <!--遮罩层-->
        <div class="mask_div"></div>
        <!--提示部分-->
        <div style="position: absolute;top: 120px;width: 100%;">
            <div style="width: 70%;margin: auto;">
                <div class="aui-text-center"
                     style="background: white;border-top-left-radius: 5px;border-top-right-radius: 5px;">
                    <div style="height: 30px;"></div>
                    <div>
                        <span class="aui-font-size-18">今日投票已用完</span>
                    </div>
                    <div class="aui-margin-t-10">
                        <span class="aui-font-size-16 text-grey-999">您可以继续为我助力</span>
                    </div>
                    <div class="aui-padded-t-15">
                        <img src="{{URL::asset('/img/present_icon.png')}}" style="width: 50%;margin: auto;">
                    </div>
                    <div style="height: 10px;"></div>
                    <div class="aui-padded-b-15">
                    <span class="aui-font-size-14 main-color"
                          style="border-radius: 36px;border: 1px solid #FF5959;height: 36px;line-height: 36px;
                          padding-left: 20px;padding-right: 20px;padding-top: 8px;padding-bottom: 8px;"
                          onclick="clickReward()">为我助力</span>
                    </div>
                    <div style="height: 10px;"></div>
                </div>
            </div>

            <div style="padding-top: 40px;">
                <img src="{{URL::asset('/img/close_white_btn.png')}}" style="width: 24px;margin: auto;"
                     onclick="closeVoteOutOfNumDiv();">
            </div>
        </div>
    </div>

    <!--助力礼物-->
    <div id="reward_gift_div" class="tip_div aui-hide" style="z-index: 999;">
        <!--遮罩层-->
        <div class="mask_div"></div>
        <!--提示部分-->
        <div style="position: absolute;bottom: 0px;width: 100%;background: white;">
            <div style="border-bottom: 1px solid #f1f1f1;">
                <span class="text-grey-999 aui-margin-l-15 font-size-12"
                      style="height: 40px;line-height: 40px;">万水千山总是情，助力一次行不行~</span>
                <img src="{{URL::asset('/img/close_red_btn.png')}}"
                     class="aui-pull-right aui-padded-r-10 aui-padded-t-10"
                     style="width: 28px;height: 28px;" onclick="closeRewardGiftTip();">
            </div>
            <div class="">
                <div class="aui-row">

                    @foreach($activity->sel_gifts as $gift)
                        <div class="aui-col-xs-4">
                            <div class="aui-margin-5">
                                <div id="gift_{{$gift->id}}" class="aui-padded-l-10 aui-padded-r-10 gift-item"
                                     onclick="selGift('{{$gift->id}}','{{$gift->price}}','{{$gift->as_vote_num}}')">
                                    <div>
                                        <div class="text-center">
                                            <img src="{{$gift->img}}"
                                                 style="width:80%;margin: auto;">
                                        </div>
                                        <div class="aui-margin-t-5 aui-text-center aui-font-size-14">
                                            <span class="text-oneline">{{$gift->name}}</span>
                                        </div>
                                        <div class="aui-margin-t-5 aui-flex-col aui-flex-middle text-grey-999 aui-font-size-14">
                                            <img src="{{URL::asset('/img/vote_money_icon_r.png')}}"
                                                 style="width: 16px;height: 16px;">
                                            <span class="aui-margin-l-10">{{$gift->price}}</span>
                                        </div>
                                        <div class="aui-flex-col aui-flex-middle text-grey-999 aui-font-size-14">
                                            <img src="{{URL::asset('/img/vote_money_icon_n.png')}}"
                                                 style="width: 16px;height: 16px;">
                                            <span class="aui-margin-l-10">{{$gift->as_vote_num}}℃</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!--需要花费金额-->
                <div style="border-top: 1px solid #f1f1f1;">
                    <div class="aui-row aui-margin-t-5 aui-padded-b-5">
                        <div class="aui-col-xs-6">
                            <div class="aui-margin-l-10">
                                <div>
                                    <span class="aui-font-size-12">需花费</span>
                                </div>
                                <div class="aui-flex-col aui-flex-middle">
                                    <img src="../img/vote_money_icon_n.png" style="width: 14px;height: 14px;">
                                    <span id="gift_vote_num_span" class="aui-font-size-12 text-grey-999">0</span>
                                    <img src="../img/vote_money_icon_r.png" style="width: 14px;height: 14px;"
                                         class="aui-margin-l-15">
                                    <span id="gift_money_span" class="aui-font-size-12 text-grey-999">0</span>
                                </div>
                            </div>
                        </div>
                        <div class="aui-col-xs-6 aui-text-center">
                            <div class="aui-flex-col aui-flex-middle aui-pull-right aui-margin-r-15 aui-margin-t-5 aui-font-size-12">
                        <span class="aui-text-center"
                              style="border: 1px solid #FF5959;padding-left: 15px;padding-right: 15px;">
                            <select id="sel_gift_num" style="height: 30px;line-height: 30px;" class="aui-font-size-12"
                                    onchange="selGiftNum();">
                                <option value="1">1个</option>
                                <option value="2">2个</option>
                                <option value="5">5个</option>
                                <option value="10">10个</option>
                                <option value="20">20个</option>
                                <option value="50">50个</option>
                                <option value="100">100个</option>
                            </select>
                        </span>
                                <span class="main-bg aui-text-center aui-text-white"
                                      style="height: 32px;line-height: 32px;padding-left: 20px;padding-right: 20px;"
                                      onclick="clickPayGift();">
                            助力
                        </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div style="height: 60px;"></div>
    <!--页脚-->
    <footer class="aui-bar aui-bar-tab" id="footer" style="border-top: 1px solid #f1f1f1;">
        <div class="aui-bar-tab-item" tapmode>
            <div class="aui-bar-tab-label aui-font-size-12 aui-border-r" onclick="clickIndex({{$activity->id}});">首页
            </div>
        </div>
        <div class="aui-bar-tab-item" tapmode>
            <div class="aui-bar-tab-label aui-font-size-12 aui-border-r" onclick="clickApply({{$activity->id}})">我要参与
            </div>
        </div>
        <div class="aui-bar-tab-item" tapmode>
            <div class="aui-bar-tab-label aui-font-size-12" onclick="clickReward();">为TA助力</div>
        </div>

        @if($vote_user->need_vote_valid_flag)
            <div class="aui-bar-tab-item main-bg" tapmode>
                <div class="aui-bar-tab-label aui-font-size-12 aui-text-white" id="TencentCaptcha"
                     data-appid="2072470952"
                     data-cbfn="validVote">支持
                </div>
            </div>
        @else
            <div class="aui-bar-tab-item main-bg" tapmode onclick="clickVote('{{$user->id}}','{{$vote_user->id}}');">
                <div class="aui-bar-tab-label aui-font-size-12 aui-text-white">支持
                </div>
            </div>
        @endif
    </footer>

@endsection

@section('script')

    <script type="text/javascript" src="{{ URL::asset('/js/aui/aui-slide.js') }}"></script>
    <script src="https://ssl.captcha.qq.com/TCaptcha.js"></script>

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

        // 是否进行项目debug
                @if(\App\Components\Utils::VOTE_SHARE_DEBUG==true)
        var share_url = 'http://' + randChars(6) + '.defwh.isart.me/vote/personShare?vote_user_id={{$vote_user->id}}';
                @else
        var share_url = window.location.href;
        @endif
        console.log("share_url:" + share_url);

        window.validVote = function (res) {
            console.log(res)
            // res（未通过验证）= {ret: 1, ticket: null}
            // res（验证成功） = {ret: 0, ticket: "String", randstr: "String"}
            if (res.ret === 0) {
                // alert(res.ticket)   // 票据
                clickVote('{{$user->id}}', '{{$vote_user->id}}')
            }
        }

        //自定义轮播
        var slide = new auiSlide({
            container: document.getElementById("aui-slide"), //容器
            // "width":300, //宽度
            "height": 220, //高度
            "speed": 500, //速度
            "autoPlay": 3000, //自动播放
            "loop": true,//是否循环
            "pageShow": true,//是否显示分页器
            "pageStyle": 'dot', //分页器样式，分dot,line
            'dotPosition': 'center' //当分页器样式为dot时控制分页器位置，left,center,right
        })

        //微信相关/////////////////////////////////////////////////////
        wx.config({!! $wxConfig !!});

        //微信配置成功后
        wx.ready(function () {
            /*
             * 进行页面分享-朋友圈
             *
             * By TerryQi
             *
             */
            wx.onMenuShareTimeline({
                title: '我是{{$vote_user->name}}，正在参加{{$activity->name}}，点击查看我的作品', // 分享标题
                link: share_url, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                imgUrl: '{{$vote_user->img_arr[0]}}', // 分享图标
                success: function (ret) {
                    // 用户确认分享后执行的回调函数
                    console.log("success ret:" + JSON.stringify(ret))
                    // alert("ret:" + JSON.stringify(ret));
                    if (ret.errMsg.indexOf("ok") > 0) {
                        var param = {
                            user_id: '{{$user->id}}',
                            vote_user_id: "{{$vote_user->id}}",
                            type: '0',
                            _token: '{{ csrf_token() }}'
                        }
                        v1_shareVoteUser("{{URL::asset('')}}", param, function (ret) {
                            console.log("ret:" + JSON.stringify(ret));
                        });
                    }
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });

            //app分享
            wx.onMenuShareAppMessage({
                title: '我是{{$vote_user->name}}，正在参加{{$activity->name}}，点击查看我的作品', // 分享标题
                desc: '{{$activity->share_desc}}',
                link: share_url, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                imgUrl: '{{$vote_user->img_arr[0]}}', // 分享图标
                type: 'link', // 分享类型,music、video或link，不填默认为link
                dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                success: function (ret) {
                    // 用户确认分享后执行的回调函数
                    console.log("success ret:" + JSON.stringify(ret))
                    // alert("ret:" + JSON.stringify(ret));

                    if (ret.errMsg.indexOf("ok") > 0) {
                        var param = {
                            user_id: '{{$user->id}}',
                            vote_user_id: "{{$vote_user->id}}",
                            type: '1',
                            _token: '{{ csrf_token() }}'
                        }
                        v1_shareVoteUser("{{URL::asset('')}}", param, function (ret) {
                            console.log("ret:" + JSON.stringify(ret));
                        });
                    }
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });
        });

        ///////////////////////////////////////////////////////////////

        //入口函数
        $(function () {


        });

        //点击投诉
        function clickComplain(vote_user_id, activity_id) {
            toast_loading("跳转举报...");
            window.location.href = "{{URL::asset('/vote/complain')}}?vote_user_id=" + vote_user_id + "&activity_id=" + activity_id;
        }

        //点击首页
        function clickIndex(activity_id) {
            toast_loading("主页跳转...");
            window.location.href = "{{URL::asset('/vote/index')}}?activity_id=" + activity_id;

        }

        //点击报名
        function clickApply(activity_id) {
            toast_loading("参赛报名...");
            window.location.href = "{{URL::asset('/vote/apply')}}?activity_id=" + activity_id;
        }

        //点击关闭
        function closeVoteSuccessTip() {
            $("#vote_success_div").addClass('aui-hide');
            toast_loading("刷新...");
            location.reload();
        }

        //点击关闭助力礼物页面
        function closeRewardGiftTip() {
            $("#reward_gift_div").addClass('aui-hide');
        }

        //点击关闭投票超出数量提示
        function closeVoteOutOfNumDiv() {
            $("#vote_outof_num_div").addClass('aui-hide');
        }

        //选中礼品
        var sel_gift_price = 0;
        var sel_gift_id = 0;
        var sel_gift_num = 1;
        var sel_gift_as_vote_num = 0;

        //点击礼品
        function selGift(gift_id, gift_price, gift_as_vote_num) {
            //选中礼品信息
            sel_gift_num = $("#sel_gift_num").val();
            sel_gift_id = gift_id;
            sel_gift_as_vote_num = gift_as_vote_num;
            sel_gift_price = gift_price;

            $("#gift_money_span").text(sel_gift_num * sel_gift_price);
            $("#gift_vote_num_span").text(sel_gift_num * sel_gift_as_vote_num);

            $(".gift-item").removeClass('sel-gift');
            $(".gift-item").addClass('no-sel-gift');
            $("#gift_" + gift_id).addClass('sel-gift');
            $("#gift_" + gift_id).removeClass('no-sel-gift');
        }

        //点击助力
        function clickPayGift() {
            //合规校验
            if (sel_gift_num == 0) {
                dialog_show({msg: '请选择礼物数量'}, null);
                return;
            }
            if (sel_gift_id == 0) {
                dialog_show({msg: '请选择礼物'}, null);
                return;
            }
            toast_loading("下单中...");
            var param = {
                user_id: '{{$user->id}}',
                vote_user_id: '{{$vote_user->id}}',
                gift_id: sel_gift_id,
                gift_num: sel_gift_num,
                _token: '{{ csrf_token() }}'
            }
            v1_vote_payOrder("{{URL::asset('')}}", param, function (ret) {
                console.log("ret:" + JSON.stringify(ret));
                // alert(JSON.stringify(ret));
                //提交成功
                if (ret.result == true) {
                    var config = ret.ret;
                    console.log("config:" + JSON.stringify(config));
                    //调起微信支付
                    wx.chooseWXPay({
                        timestamp: config.timeStamp, // 支付签名时间戳，注意微信jssdk中的所有使用timestamp字段均为小写。但最新版的支付后台生成签名使用的timeStamp字段名需大写其中的S字符
                        nonceStr: config.nonceStr, // 支付签名随机串，不长于 32 位
                        package: config.package, // 统一支付接口返回的prepay_id参数值，提交格式如：prepay_id=\*\*\*）
                        signType: config.signType, // 签名方式，默认为'SHA1'，使用新版支付需传入'MD5'
                        paySign: config.paySign, // 支付签名
                        success: function (res) {
                            // 支付成功后的回调函数
                            console.log("res:" + JSON.stringify(res));
                            toast_loading("刷新...");
                            location.reload();
                        }
                    });
                }
            });

        }

        //选中数量
        function selGiftNum() {
            sel_gift_num = $("#sel_gift_num").val();
            $("#gift_money_span").text(sel_gift_num * sel_gift_price);
            $("#gift_vote_num_span").text(sel_gift_num * sel_gift_as_vote_num);
        }

        //点击助力
        function clickReward() {
            $("#vote_success_div").addClass('aui-hide');
            $("#vote_outof_num_div").addClass('aui-hide');
            $("#reward_gift_div").removeClass('aui-hide');
        }

        //点击支持-投票
        function clickVote(user_id, vote_user_id) {
            var param = {
                user_id: user_id,
                vote_user_id: vote_user_id,
                _token: '{{ csrf_token() }}'
            }
            toast_loading("投票中...");
            v1_vote("{{URL::asset('')}}", param, function (ret) {
                console.log("ret:" + JSON.stringify(ret));
                //提交成功
                if (ret.result == true) {
                    //计算票数
                    var latest_vote_num = {{$vote_user->vote_num}}+ret.ret.vote_num;
                    $("#latest_vote_num_span").text(latest_vote_num);
                    $("#vote_success_div").removeClass('aui-hide');
                } else {
                    //如果是投票数用完
                    if (ret.code == '301') {
                        $("#vote_outof_num_div").removeClass('aui-hide');
                    } else {
                        dialog_show({msg: ret.message, buttons: ['确定']}, null);
                    }
                }
                toast_hide();
            })
        }

        //点击关注
        function clickGuanZhu(user_id, vote_user_id) {
            var param = {
                user_id: user_id,
                vote_user_id: vote_user_id,
                _token: '{{ csrf_token() }}'
            }
            toast_loading("关注TA...");
            v1_voteUser_guanzhu("{{URL::asset('')}}", param, function (ret) {
                console.log("ret:" + JSON.stringify(ret));
                //提交成功
                if (ret.result == true) {
                    $("#guanzhu_div").text("已关注");
                    window.location.href = "{{URL::asset('/vote/guanzhu')}}?vote_user_id=" + vote_user_id;
                } else {
                    dialog_show({msg: ret.message, buttons: ['确定']}, null);
                }
                toast_hide();
            })
        }

        //点击查看更多
        function clickMore() {
            $(".order-list-div").removeClass('aui-hide');
            $("#show-more-div").addClass('aui-hide');
        }

        //点击发送证书
        function clickSendCert(vote_user_id, activity_id) {
            window.location.href = "{{URL::asset('/vote/sendCert')}}?vote_user_id=" + vote_user_id + "&activity_id=" + activity_id;
        }


    </script>
@endsection