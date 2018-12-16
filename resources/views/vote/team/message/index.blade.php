@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 通知消息管理 <span
                class="c-gray en">&gt;</span> 通知消息列表 <a class="btn btn-success radius r btn-refresh"
                                                        style="line-height:1.6em;margin-top:3px"
                                                        title="刷新"
                                                        onclick="location.replace('{{route('team.message.index', $con_arr)}}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>
    <div class="page-container">

        <div class="text-c">
            <form id="search_form" action="{{route('team.message.index')}}" method="post"
                  class="form-horizontal">
                {{csrf_field()}}
                <div class="Huiform text-r">
                    <input id="search_word" name="search_word" type="text" class="input-text" style="width:400px"
                           placeholder="按标题搜索，支持模糊搜索" value="{{$con_arr['search_word']}}">
                    <button type="submit" class="btn btn-success" id="" name="">
                        <i class="Hui-iconfont">&#xe665;</i> 搜索
                    </button>
                </div>
            </form>
        </div>


        <style>
            .mod_default_box {
                background-color: #fff;
                border-radius: 4px;
                box-shadow: 0 1px 2px rgba(150, 150, 150, 0.3);
                padding: 20px 30px 30px;
            }

            .mod_default_box {
                padding: 0;
            }

            div {
                display: block;
            }

            .notice_item {
                position: relative;
                word-wrap: break-word;
                word-break: break-all;
                cursor: pointer;
                padding: 26px 30px;
            }

            body, h1, h2, h3, h4, h5, h6, p, ul, ol, dl, dd, fieldset, textarea {
                margin: 0;
            }

            .notice_item.readed .notice_title_wrp {
                color: #9a9a9a;
            }

            .notice_title_wrp {
                display: block;
                padding-right: 12em;
                position: relative;
                color: #353535;
                text-decoration: none;
            }

            a {
                color: #576b95;
                text-decoration: none;
            }

            input, textarea, button, a {
                outline: 0;
            }

            .notice_title_wrp .notice_title {
                display: block;
                width: auto;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                word-wrap: normal;
                font-weight: 400;
            }

            .icon_notice_dot {
                margin-right: 1em;
                display: none;
            }

            .icon_dot_level_0 {
                display: inline-block;
                font-weight: 400;
                font-style: normal;
                vertical-align: middle;
                margin-top: -0.2em;
                margin-right: 5px;
                font-size: 14px;
                color: #d5d5d5;
            }

            .icon_dot_level_1 {
                display: inline-block;
                font-weight: 400;
                font-style: normal;
                vertical-align: middle;
                margin-top: -0.2em;
                margin-right: 5px;
                font-size: 14px;
                color: #f37b1d;
            }

            .icon_dot_level_2 {
                display: inline-block;
                font-weight: 400;
                font-style: normal;
                vertical-align: middle;
                margin-top: -0.2em;
                margin-right: 5px;
                font-size: 14px;
                color: #dd514c;
            }

            .notice_title_wrp .notice_time {
                position: absolute;
                top: 0;
                right: 4em;
            }

            .notice_time {
                color: #9a9a9a;
            }

            .icon_notice_arrow {
                position: absolute;
                top: 50%;
                margin-top: -4px;
                right: 1em;
                display: inline-block;
                width: 0;
                height: 0;
                border-width: 4px;
                border-style: dashed;
                border-color: transparent;
                border-bottom-width: 0;
                border-top-color: #e7e7eb;
                border-top-style: solid;
            }

            .notice_item dd {
                padding: 9px 4em 0 0;
                color: #9a9a9a;
            }

            .dn {
                display: none;
            }

            .notice_item.select {
                border-radius: 4px;
                -moz-border-radius: 4px;
                -webkit-border-radius: 4px;
                box-shadow: 0 0 20px #e4e8eb;
                -moz-box-shadow: 0 0 20px #e4e8eb;
                -webkit-box-shadow: 0 0 20px #e4e8eb;
                background-color: #fff;
                border-radius: 0;
                -moz-border-radius: 0;
                -webkit-border-radius: 0;
            }

            .notice_item.select:before {
                left: 0;
                right: 0;
            }

            .notice_item:before {
                content: " ";
                position: absolute;
                bottom: 0;
                left: 30px;
                right: 30px;
                border-bottom: 1px solid #e7e7eb;
            }

            .pagination_wrp {
                text-align: right;
            }

            .pagination_wrp {
                padding: 20px 30px;
            }
        </style>

        <div class="mod_default_box" style="margin-top: 20px;">
            <div id="notification" class="notice_list">

                @foreach($datas as $data)
                    <dl class="notice_item readed  js_msg_item">
                        <dt>
                            <a class="notice_title_wrp" href="javascript:;"
                               onclick="clickOpenDetail('通知详情 - {{$data->title}}', '{{route('team.message.info', ['id' => $data->id])}}')">
                                <strong class="notice_title">
                                    @if ($data->level == 0)
                                        <i class="icon_dot_level_0 icon_notice_dot">●</i>
                                    @elseif ($data->level == 1)
                                        <i class="icon_dot_level_1 icon_notice_dot">●</i>
                                    @elseif ($data->level == 2)
                                        <i class="icon_dot_level_2 icon_notice_dot">●</i>
                                    @endif

                                    {{$data->title}}
                                    @if ($data->top == '1')
                                        <span class="label label-primary radius"
                                              style="margin-left: 10px;">{{$data->top_str}}</span>
                                    @endif
                                </strong>
                                <span class="notice_time">
                                    {{substr($data->created_at, 0, 10)}}
                                </span>
                                {{--<i class="icon_notice_arrow"></i>--}}
                            </a>
                        </dt>
                    </dl>
                @endforeach
            </div>
            {{--<div id="pagebar" class="pagination_wrp">--}}
            {{ $datas->appends($con_arr)->links() }}
            {{--</div>--}}
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">


        $(function () {

        });

        function clickOpenDetail(title, url) {
            var index = layer.open({
                type: 2,
                title: title,
                content: url
            });
            layer.full(index);
        }


    </script>
@endsection