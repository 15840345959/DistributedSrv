@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 参与信息 <span
                class="c-gray en">&gt;</span> 参与活动明细列表 <a class="btn btn-success radius r btn-refresh"
                                                          style="line-height:1.6em;margin-top:3px"
                                                          title="刷新"
                                                          onclick="location.replace('{{URL::asset('/admin/mryh/mryhJoin/index')}}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>
    <div class="page-container">

        <div class="text-c">
            <form id="search_form" action="{{URL::asset('/admin/mryh/mryhJoin/index')}}" method="post"
                  class="form-horizontal">
                {{csrf_field()}}
                <div class="Huiform text-r">
                    <span>用户id</span>
                    <input id="user_id" name="user_id" type="text" class="input-text"
                           style="width:100px"
                           placeholder="用户id" value="{{$con_arr['user_id']}}">
                    <span class="ml-10">活动id</span>
                    <input id="game_id" name="game_id" type="text" class="input-text"
                           style="width:100px"
                           placeholder="活动id" value="{{$con_arr['game_id']}}">
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
                    <th scope="col" colspan="7">参与活动明细列表
                        <span class="r">共有数据：<strong>{{$datas->total()}}</strong> 条</span></th>
                </tr>
                <tr class="text-c">
                    {{--<th width="25"><input type="checkbox" name="" value=""></th>--}}
                    <th width="50">ID</th>
                    <th width="100">活动名称</th>
                    <th width="100">用户</th>
                    <th width="150">参赛明细信息</th>
                    <th width="80">清分信息</th>
                    <th width="80">结算信息</th>
                    <th width="60">操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($datas as $data)
                    <tr class="text-c">
                        <td>
                            <span>{{$data->id}}</span>
                        </td>
                        <td>
                            <div>
                            <span class="c-primary" style="cursor: pointer;"
                                  onclick="creatIframe('{{URL::asset('/admin/mryh/mryhGame/index')}}?id={{$data->game->id}}', '活动信息-{{$data->game->name}}');">{{$data->game->name}}
                                ({{$data->game->id}})</span>
                            </div>
                            <div class="mt-5">
                                参赛金额：<span class="label label-danger">{{$data->game->join_price}}</span>
                            </div>
                            <div class="mt-5">
                                实际缴费：<span
                                        class="label label-primary">{{isset($data->order)?$data->order->total_fee:'0'}}</span>
                            </div>
                            <div class="mt-5">
                                目标天数：<span class="label label-secondary">{{$data->game->target_join_day}}</span>
                            </div>
                        </td>
                        <td>
                            @if($data->user)
                                <span class="c-primary" style="cursor: pointer;"
                                      onclick="creatIframe('{{URL::asset('/admin/user/info')}}?id={{$data->user->id}}', '选手信息-{{$data->user->nick_name}}');">{{$data->user->nick_name}}
                                    ({{$data->user->id}})</span>
                            @endif
                        </td>
                        <td>
                            <div>
                                关联订单：<span class="c-primary">{{$data->trade_no?$data->trade_no:'--'}}</span>
                            </div>
                            <div class="mt-5">
                                参赛时间：<span class="c-primary">{{$data->join_time}}</span>
                            </div>
                            <div class="mt-5">
                                参与天数：<span class="label label-primary">{{$data->join_day_num}}</span>
                            </div>
                            <div class="mt-5">
                                作品数目：<span class="label label-secondary">{{$data->work_num}}</span>
                            </div>
                            <div class="mt-5">
                                参与状态：<span class="c-primary">{{$data->game_status_str}}</span>
                            </div>
                        </td>
                        <td>
                            <div>
                                清分时间：<span class="c-primary">{{$data->clear_time?$data->clear_time:'--'}}</span>
                            </div>
                            <div class="mt-5">
                                清分状态：<span class="c-primary">{{$data->clear_status_str}}</span>
                            </div>
                            <div class="mt-5">
                                清分金额：<span class="label label-primary">{{$data->jiesuan_price}}</span>
                            </div>
                        </td>
                        <td>
                            <div>
                                结算时间：<span class="c-primary">{{$data->jiesuan_time?$data->jiesuan_time:'--'}}</span>
                            </div>
                            <div class="mt-5">
                                结算状态：<span class="c-primary">{{$data->jiesuan_status_str}}</span>
                            </div>
                            <div class="mt-5">
                                结算金额：<span class="label label-primary">{{$data->jiesuan_price}}</span>
                            </div>
                        </td>
                        <td>
                            <a style="text-decoration:none"
                               onClick="showJoinArticles('{{$data->id}}','{{$data->id}}')"
                               href="javascript:;" class="c-primary"
                               title="参赛作品明细">
                                参赛作品明细
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

        /*
         * 展示参赛作品明细
         *
         * By TerryQi
         *
         * 2018-09-21
         */
        function showJoinArticles(join_id, join_id) {
            creatIframe("{{URL::asset('/admin/mryh/mryhJoinArticle/index')}}?join_id=" + join_id, "参赛作品明细-" + join_id);
        }


    </script>
@endsection