@extends('admin.layouts.app')

@section('content')

    <header class="navbar-wrapper">
        <div class="navbar navbar-fixed-top">
            <div class="container-fluid cl"><a class="logo navbar-logo f-l mr-10 hidden-xs"
                                               href="">ISART</a>
                <a class="logo navbar-logo-m f-l mr-10 visible-xs" href="#"></a>
                <span class="logo navbar-slogan f-l mr-10 hidden-xs">v1.3</span>
                <a aria-hidden="false" class="nav-toggle Hui-iconfont visible-xs" href="javascript:;">&#xe667;</a>
                <nav id="Hui-userbar" class="nav navbar-nav navbar-userbar hidden-xs">
                    <ul class="cl">
                        @if($admin['role']==0)
                            <li>超级管理员</li>
                        @endif
                        @if($admin['role']==1)
                            <li>运营人员</li>
                        @endif
                        {{--<li>超级管理员</li>--}}
                        <li class="dropDown dropDown_hover">
                            <a href="#" class="dropDown_A">{{$admin['name']}}<i class="Hui-iconfont">&#xe6d5;</i></a>
                            <ul class="dropDown-menu menu radius box-shadow">
                                <li><a href="javascript:;" onClick="mysqlf_edit('修改个人信息','{{ route('editMySelf') }}')">个人信息</a>
                                </li>
                                {{--<li><a href="#">切换账户</a></li>--}}
                                <li><a href="{{ URL::asset('/admin/loginout') }}">退出</a></li>
                            </ul>
                        </li>
                        {{--<li id="Hui-msg">--}}
                        {{--<a href="#" title="消息">--}}
                        {{--<span class="badge badge-danger">1</span>--}}
                        {{--<i class="Hui-iconfont" style="font-size:18px">&#xe68a;</i>--}}
                        {{--</a>--}}
                        {{--</li>--}}
                        <li id="Hui-skin" class="dropDown right dropDown_hover">
                            <a href="javascript:;" class="dropDown_A" title="换肤">
                                <i class="Hui-iconfont" style="font-size:18px">&#xe62a;</i>
                            </a>
                            <ul class="dropDown-menu menu radius box-shadow">
                                <li><a href="javascript:;" data-val="default" title="默认（黑色）">默认（黑色）</a></li>
                                <li><a href="javascript:;" data-val="blue" title="蓝色">蓝色</a></li>
                                <li><a href="javascript:;" data-val="green" title="绿色">绿色</a></li>
                                <li><a href="javascript:;" data-val="red" title="红色">红色</a></li>
                                <li><a href="javascript:;" data-val="yellow" title="黄色">黄色</a></li>
                                <li><a href="javascript:;" data-val="orange" title="橙色">橙色</a></li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>
    <aside class="Hui-aside">
        <div class="menu_dropdown bk_2">


            <dl id="menu-article">
                <dt>
                    <a data-href="{{ route('overview.index') }}" data-title="业务预览" href="javascript:void(0)"
                       style="text-decoration: none;line-height: 32px;border-bottom: none;font-weight: normal;">业务概览</a>
                </dt>
            </dl>

            <dl id="menu-article">
                <dt>管理员管理<i class="Hui-iconfont menu_dropdown-arrow">
                        &#xe6d5;</i>
                </dt>
                <dd>
                    <ul>
                        <li><a data-href="{{ URL::asset('/admin/admin/index') }}" data-title="管理员管理"
                               href="javascript:void(0)">管理员管理</a></li>
                    </ul>
                </dd>
            </dl>
            <dl id="menu-article">
                <dt>用户管理<i class="Hui-iconfont menu_dropdown-arrow">
                        &#xe6d5;</i>
                </dt>
                <dd>
                    <ul>
                        <li><a data-href="{{ URL::asset('/admin/user/index') }}" data-title="用户管理"
                               href="javascript:void(0)">用户管理</a></li>
                        <li><a data-href="{{ URL::asset('/admin/guanzhu/index') }}" data-title="用户关注明细"
                               href="javascript:void(0)">用户关注明细</a></li>
                        <li><a data-href="{{ URL::asset('/admin/userRel/index') }}" data-title="用户关系明细"
                               href="javascript:void(0)">用户关系明细</a></li>
                    </ul>
                </dd>
            </dl>
            <dl id="menu-article">
                <dt>小程序消息<i class="Hui-iconfont menu_dropdown-arrow">
                        &#xe6d5;</i>
                </dt>
                <dd>
                    <ul>
                        <li><a data-href="{{ URL::asset('/admin/xcxForm/index') }}" data-title="小程序消息"
                               href="javascript:void(0)">小程序消息</a></li>
                    </ul>
                </dd>
            </dl>
            <dl id="menu-article">
                <dt>作品管理<i class="Hui-iconfont menu_dropdown-arrow">
                        &#xe6d5;</i>
                </dt>
                <dd>
                    <ul>
                        <li><a data-href="{{ URL::asset('/admin/article/index') }}" data-title="作品明细"
                               href="javascript:void(0)">作品明细</a></li>
                        <li><a data-href="{{ URL::asset('/admin/articleType/index') }}" data-title="作品类别"
                               href="javascript:void(0)">作品类别</a></li>
                    </ul>
                </dd>
            </dl>
            <dl id="menu-article">
                <dt>商品管理<i class="Hui-iconfont menu_dropdown-arrow">
                        &#xe6d5;</i>
                </dt>
                <dd>
                    <ul>
                        <li><a data-href="{{ URL::asset('/admin/goods/index') }}" data-title="商品明细"
                               href="javascript:void(0)">商品明细<span
                                        class="label label-danger radius ml-10">!</span></a></li>
                        <li><a data-href="{{ URL::asset('/admin/goodsType/index') }}" data-title="商品类别"
                               href="javascript:void(0)">商品类别</a></li>
                        <li><a data-href="{{ URL::asset('/admin/logisticsSetting/index') }}" data-title="物流费配置"
                               href="javascript:void(0)">物流费配置</a></li>
                    </ul>
                </dd>
            </dl>
            <dl id="menu-article">
                <dt>规则相关<i class="Hui-iconfont menu_dropdown-arrow">
                        &#xe6d5;</i>
                </dt>
                <dd>
                    <ul>
                        <li><a data-href="{{ URL::asset('admin/rule/index') }}"
                               data-title="规则管理"
                               href="javascript:void(0)">规则管理</a>
                        </li>

                    </ul>
                </dd>
            </dl>
            <dl id="menu-article">
                <dt>点赞、评论、收藏、意见<i class="Hui-iconfont menu_dropdown-arrow">
                        &#xe6d5;</i>
                </dt>
                <dd>
                    <ul>
                        <li><a data-href="{{ URL::asset('/admin/zan/index') }}" data-title="点赞明细"
                               href="javascript:void(0)">点赞明细</a></li>
                        <li><a data-href="{{ URL::asset('/admin/comment/index') }}" data-title="评论明细"
                               href="javascript:void(0)">评论明细</a></li>
                        <li><a data-href="{{ URL::asset('/admin/collect/index') }}" data-title="收藏明细"
                               href="javascript:void(0)">收藏明细</a></li>
                        <li><a data-href="{{ URL::asset('/admin/feedback/index') }}" data-title="意见反馈"
                               href="javascript:void(0)">意见反馈</a></li>
                    </ul>
                </dd>
            </dl>
            <dl id="menu-operateValue">
                <dt>ISART服务号配置<i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i>
                </dt>
                <dd>
                    <ul>
                        <li><a data-href="{{ URL::asset('admin/gzh/material/index') }}?busi_name=isart"
                               data-title="素材管理"
                               href="javascript:void(0)">素材管理</a>
                        </li>
                        <li><a data-href="{{ URL::asset('admin/gzh/menu/index') }}?busi_name=isart" data-title="菜单配置"
                               href="javascript:void(0)">菜单配置</a>
                        </li>
                        <li><a data-href="{{ URL::asset('admin/gzh/reply/index') }}?busi_name=isart" data-title="关键词回复"
                               href="javascript:void(0)">关键词回复</a>
                        </li>
                        <li><a data-href="{{ URL::asset('admin/gzh/busiWord/index') }}?busi_name=isart"
                               data-title="业务话术"
                               href="javascript:void(0)">业务话术</a>
                        </li>
                        <li><a data-href="{{ URL::asset('admin/gzh/directMessage/edit') }}?busi_name=isart"
                               data-title="定向客服消息"
                               href="javascript:void(0)">定向客服消息</a>
                        </li>
                        <li><a data-href="{{ URL::asset('admin/gzh/base/isart/info') }}?busi_name=isart"
                               data-title="公众号信息"
                               href="javascript:void(0)">公众号信息<span
                                        class="label label-danger radius ml-10">!</span></a>
                        </li>
                    </ul>
                </dd>
            </dl>
            <dl id="menu-operateValue">
                <dt>投票大赛<i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i>
                </dt>
                <dd>
                    <ul>
                        <li><a data-href="{{ URL::asset('admin/vote/voteStmt/daily') }}"
                               data-title="管理日报"
                               href="javascript:void(0)">管理日报<span
                                        class="label label-danger radius ml-10">!</span></a>
                        </li>
                        <li><a data-href="{{ URL::asset('admin/vote/voteAD/index') }}"
                               data-title="广告管理"
                               href="javascript:void(0)">广告管理</a>
                        </li>
                        <li><a data-href="{{ URL::asset('admin/vote/voteGift/index') }}"
                               data-title="礼品管理"
                               href="javascript:void(0)">礼品管理</a>
                        </li>
                        <li><a data-href="{{ URL::asset('admin/vote/voteTeam/index') }}"
                               data-title="地推团队"
                               href="javascript:void(0)">地推团队</a>
                        </li>
                        <li><a data-href="{{ URL::asset('admin/vote/voteRule/index') }}"
                               data-title="大赛规则统管"
                               href="javascript:void(0)">大赛规则统管</a>
                        </li>
                        <li><a data-href="{{ URL::asset('admin/vote/voteActivity/index') }}"
                               data-title="大赛管理"
                               href="javascript:void(0)">大赛管理<span
                                        class="label label-danger radius ml-10">!</span></a>
                        </li>
                        <li><a data-href="{{ URL::asset('admin/vote/voteUser/index') }}"
                               data-title="选手管理"
                               href="javascript:void(0)">选手管理</a>
                        </li>
                        <li><a data-href="{{ URL::asset('admin/vote/voteRecord/index') }}"
                               data-title="投票明细"
                               href="javascript:void(0)">投票明细</a>
                        </li>
                        <li><a data-href="{{ URL::asset('admin/vote/voteOrder/index') }}"
                               data-title="打赏明细"
                               href="javascript:void(0)">打赏明细<span
                                        class="label label-danger radius ml-10">!</span></a>
                        </li>
                        <li><a data-href="{{ URL::asset('admin/vote/voteShareRecord/index') }}"
                               data-title="分享明细"
                               href="javascript:void(0)">分享明细</a>
                        </li>
                        <li><a data-href="{{ URL::asset('admin/vote/voteGuanZhu/index') }}"
                               data-title="关注明细"
                               href="javascript:void(0)">关注明细</a>
                        </li>
                        <li><a data-href="{{ URL::asset('admin/vote/voteComplain/index') }}?status=0"
                               data-title="投诉管理"
                               href="javascript:void(0)">投诉管理</a>
                        </li>
                        <li><a data-href="{{ URL::asset('admin/vote/voteCertSend/index') }}"
                               data-title="证书下载"
                               href="javascript:void(0)">证书下载</a>
                        </li>
                    </ul>
                </dd>
            </dl>
            <dl id="menu-operateValue">
                <dt>每天一画<i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i>
                </dt>
                <dd>
                    <ul>
                        <li><a data-href="{{ URL::asset('admin/mryh/mryhOverview/index') }}"
                               data-title="每天一画业务概览"
                               href="javascript:void(0)">业务概览<span
                                        class="label label-danger radius ml-10">!</span></a>
                        </li>
                        <li><a data-href="{{ URL::asset('admin/mryh/mryhAD/index') }}"
                               data-title="广告位管理"
                               href="javascript:void(0)">广告位管理</a>
                        </li>
                        <li><a data-href="{{ URL::asset('admin/rule/index') }}?busi_name=mryh"
                               data-title="规则管理"
                               href="javascript:void(0)">规则管理</a>
                        </li>
                        <li><a data-href="{{ URL::asset('admin/mryh/mryhUser/index') }}"
                               data-title="用户管理"
                               href="javascript:void(0)">用户管理</a>
                        </li>
                        <li><a data-href="{{ URL::asset('admin/mryh/mryhCoupon/index') }}"
                               data-title="优惠券管理"
                               href="javascript:void(0)">优惠券管理</a>
                        </li>
                        <li><a data-href="{{ URL::asset('admin/mryh/mryhUserCoupon/index') }}"
                               data-title="优惠券派发明细"
                               href="javascript:void(0)">优惠券派发明细</a>
                        </li>
                        <li><a data-href="{{ URL::asset('admin/mryh/mryhSetting/index') }}"
                               data-title="业务配置"
                               href="javascript:void(0)">业务配置</a>
                        </li>
                        <li><a data-href="{{ URL::asset('admin/mryh/mryhGame/index') }}"
                               data-title="活动管理"
                               href="javascript:void(0)">活动管理<span
                                        class="label label-danger radius ml-10">!</span></a>
                        </li>
                        <li><a data-href="{{ URL::asset('admin/mryh/mryhJoin/index') }}"
                               data-title="参赛明细信息"
                               href="javascript:void(0)">参赛明细信息</a>
                        </li>
                        <li><a data-href="{{ URL::asset('admin/mryh/mryhJoinArticle/index') }}"
                               data-title="参赛作品明细"
                               href="javascript:void(0)">参赛作品明细</a>
                        </li>
                        <li><a data-href="{{ URL::asset('admin/mryh/mryhJoinOrder/index') }}"
                               data-title="订单管理"
                               href="javascript:void(0)">订单管理</a>
                        </li>
                        <li><a data-href="{{ URL::asset('admin/mryh/mryhWithdrawCash/index') }}"
                               data-title="提现明细"
                               href="javascript:void(0)">提现明细<span
                                        class="label label-danger radius ml-10">!</span></a>
                        </li>
                        <li><a data-href="{{ URL::asset('/admin/feedback/index') }}?busi_name=mryh" data-title="意见反馈"
                               href="javascript:void(0)">意见反馈<span
                                        class="label label-danger radius ml-10">!</span></a></li>
                        <li><a data-href="{{ URL::asset('admin/mryh/mryhComputePrize/index') }}"
                               data-title="清分明细"
                               href="javascript:void(0)">清分明细</a>
                        </li>
                        <li><a data-href="{{ URL::asset('admin/mryh/mryhCertSend/index') }}"
                               data-title="证书下载"
                               href="javascript:void(0)">证书下载</a>
                        </li>
                    </ul>
                </dd>
            </dl>

            <dl id="menu-operateValue">
                <dt>艺术榜<i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i>
                </dt>
                <dd>
                    <ul>
                        <li><a data-href="{{ URL::asset('admin/ysb/ysbAD/index') }}"
                               data-title="广告位管理"
                               href="javascript:void(0)">广告位管理</a>
                        </li>
                        <li><a data-href="{{ URL::asset('admin/ysb/ysbUser/index') }}"
                               data-title="用户管理"
                               href="javascript:void(0)">用户管理</a>
                        </li>
                        <li><a data-href="{{ URL::asset('/admin/article/index') }}?busi_name=ysb" data-title="作品明细"
                               href="javascript:void(0)">作品明细</a></li>
                        <li><a data-href="{{ URL::asset('/admin/feedback/index') }}?busi_name=ysb" data-title="意见反馈"
                               href="javascript:void(0)">意见反馈<span
                                        class="label label-danger radius ml-10">!</span></a></li>
                        <li><a data-href="{{ URL::asset('admin/rule/index') }}?busi_name=ysb"
                               data-title="规则管理"
                               href="javascript:void(0)">规则管理</a>
                        </li>
                    </ul>
                </dd>
            </dl>
            <dl id="menu-operateValue">
                <dt>营销活动<i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i>
                </dt>
                <dd>
                    <ul>
                        <li><a data-href="{{ URL::asset('admin/yxhd/yxhdActivity/index') }}"
                               data-title="活动管理"
                               href="javascript:void(0)">活动管理<span
                                        class="label label-danger radius ml-10">!</span></a>
                        </li>
                        <li><a data-href="{{ URL::asset('admin/yxhd/yxhdPrize/index') }}"
                               data-title="奖品管理"
                               href="javascript:void(0)">奖品管理</a>
                        </li>
                        <li><a data-href="{{ URL::asset('admin/yxhd/yxhdOrder/index') }}"
                               data-title="抽奖记录管理"
                               href="javascript:void(0)">抽奖记录管理</a>
                        </li>
                    </ul>
                </dd>
            </dl>
            <dl id="menu-operateValue">
                <dt>小艺商城<i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i>
                </dt>
                <dd>
                    <ul>
                        <li><a data-href="{{ URL::asset('admin/shop/shopAD/index') }}"
                               data-title="广告位管理"
                               href="javascript:void(0)">广告位管理</a>
                        </li>
                        <li><a data-href="{{ URL::asset('admin/shop/shopUser/index') }}"
                               data-title="用户管理"
                               href="javascript:void(0)">用户管理</a>
                        </li>
                        <li><a data-href="{{ URL::asset('/admin/goods/index') }}?busi_name=shop" data-title="商品明细"
                               href="javascript:void(0)">商品明细</a></li>
                        <li><a data-href="{{ URL::asset('admin/shop/shopOrder/index') }}"
                               data-title="订单管理"
                               href="javascript:void(0)">订单管理</a>
                        </li>
                        <li><a data-href="{{ URL::asset('/admin/feedback/index') }}?busi_name=shop" data-title="意见反馈"
                               href="javascript:void(0)">意见反馈<span
                                        class="label label-danger radius ml-10">!</span></a></li>
                        <li><a data-href="{{ URL::asset('admin/rule/index') }}?busi_name=shop"
                               data-title="规则管理"
                               href="javascript:void(0)">规则管理</a>
                        </li>
                    </ul>
                </dd>
            </dl>

            <dl id="menu-operateValue">
                <dt>配置管理<i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i>
                </dt>
                <dd>
                    <ul>
                        <li>
                            <a data-href="{{ route('optInfo.index') }}"
                               data-title="操作值管理"
                               href="javascript:void(0)">操作值管理</a>
                        </li>
                    </ul>
                </dd>
            </dl>

            <dl id="menu-article">
                <dt>商户业务<i class="Hui-iconfont menu_dropdown-arrow">
                        &#xe6d5;</i>
                </dt>
                <dd>
                    <ul>
                        <li><a data-href="{{ route('admin.b.message.index') }}"
                               data-title="消息通知"
                               href="javascript:void(0)">消息通知</a>
                        </li>
                    </ul>
                </dd>
            </dl>
        </div>
    </aside>
    <div class="dislpayArrow hidden-xs"><a class="pngfix" href="javascript:void(0);" onClick="displaynavbar(this)"></a>
    </div>

    <section class="Hui-article-box">
        <div id="Hui-tabNav" class="Hui-tabNav hidden-xs">
            <div class="Hui-tabNav-wp">
                <ul id="min_title_list" class="acrossTab cl">
                    <li class="active">
                        <span title="业务概览" data-href="{{ route('overview.index') }}">业务概览</span>
                        <em></em>
                    </li>
                </ul>
            </div>
            <div class="Hui-tabNav-more btn-group">
                <a id="js-tabNav-prev" class="btn radius btn-default size-S" href="javascript:;">
                    <i class="Hui-iconfont">&#xe6d4;</i>
                </a>
                <a id="js-tabNav-next" class="btn radius btn-default size-S" href="javascript:;">
                    <i class="Hui-iconfont">&#xe6d7;</i>
                </a>
            </div>
        </div>
        <div id="iframe_box" class="Hui-article">
            <div class="show_iframe">
                <div style="display:none" class="loading"></div>
                <iframe scrolling="yes" frameborder="0" src="{{ route('overview.index') }}"></iframe>
            </div>
        </div>
    </section>

    <div class="contextMenu" id="Huiadminmenu">
        <ul>
            <li id="closethis">关闭当前</li>
            <li id="closeall">关闭全部</li>
        </ul>
    </div>

@endsection

@section('script')
    <script type="text/javascript">
        $(function () {

        });

        /*个人信息-修改*/
        function mysqlf_edit(title, url) {
            var index = layer.open({
                type: 2,
                title: title,
                content: url
            });
            layer.full(index);
        }

    </script>
@endsection