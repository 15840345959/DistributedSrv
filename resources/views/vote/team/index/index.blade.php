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
                        <li class="dropDown dropDown_hover">
                            <a href="#" class="dropDown_A">{{$team['name']}}<i class="Hui-iconfont">&#xe6d5;</i></a>
                            <ul class="dropDown-menu menu radius box-shadow">
                                {{--<li><a href="javascript:;" onClick="mysqlf_edit('修改个人信息','{{ route('editMySelf') }}')">个人信息</a>--}}
                                </li>
                                <li><a href="{{ route('team.loginout') }}">退出</a></li>
                            </ul>
                        </li>
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
                    <a data-href="{{ route('team.overview.index') }}" data-title="业务预览" href="javascript:void(0)"
                       style="text-decoration: none;line-height: 32px;border-bottom: none;font-weight: normal;">业务概览</a>
                </dt>
            </dl>

            <dl id="menu-article">
                <dt>
                    <a data-href="{{ route('team.message.index') }}" data-title="消息通知" href="javascript:void(0)"
                       style="text-decoration: none;line-height: 32px;border-bottom: none;font-weight: normal;">消息通知<span
                                class="label label-danger radius ml-10">!</span></a>
                </dt>
            </dl>

            <dl id="menu-operateValue">
                <dt>投票大赛<i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i>
                </dt>
                <dd>
                    <ul>
                        <li>
                            <a data-href="{{ route('team.activity.index') }}"
                               data-title="大赛管理"
                               href="javascript:void(0)">大赛管理</a>
                        </li>
                        <li>
                            <a data-href="{{ route('team.voteUser.index', ['audit_status' => '0']) }}"
                               data-title="选手管理"
                               href="javascript:void(0)">选手管理<span
                                        class="label label-danger radius ml-10">!</span></a>
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
                        <span title="业务概览" data-href="{{ route('team.overview.index') }}">业务概览</span>
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
                <iframe scrolling="yes" frameborder="0" src="{{ route('team.overview.index') }}"></iframe>
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