@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 参与信息 <span
                class="c-gray en">&gt;</span> 参与活动作品列表 <a class="btn btn-success radius r btn-refresh"
                                                          style="line-height:1.6em;margin-top:3px"
                                                          title="刷新"
                                                          onclick="location.replace('{{URL::asset('/admin/mryh/mryhJoinArticle/index')}}');"><i
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
                    <span class="ml-10">参赛记录id</span>
                    <input id="join_id" name="join_id" type="text" class="input-text"
                           style="width:100px"
                           placeholder="参赛记录id" value="{{$con_arr['join_id']}}">
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
                    <th scope="col" colspan="6">参与活动作品列表
                        <span class="r">共有数据：<strong>{{$datas->total()}}</strong> 条</span></th>
                </tr>
                <tr class="text-c">
                    {{--<th width="25"><input type="checkbox" name="" value=""></th>--}}
                    <th width="50">ID</th>
                    <th width="100">大赛信息</th>
                    <th width="100">用户信息</th>
                    <th width="100">参赛记录信息</th>
                    <th width="100">作品信息</th>
                </tr>
                </thead>
                <tbody>
                @foreach($datas as $data)
                    <tr class="text-c">
                        <td>
                            <span>{{$data->id}}</span>
                        </td>
                        <td>
                            <div class="mt-5">
                                <img src="{{$data->game->img}}" style="width: 80px;">
                            </div>
                            <div class="mt-5">
                                <span class="c-primary" style="cursor: pointer;"
                                      onclick="creatIframe('{{URL::asset('/admin/mryh/mryhGame/index')}}?id={{$data->game->id}}', '活动信息-{{$data->game->name}}');">{{$data->game->name}}
                                    ({{$data->game->id}})</span>
                            </div>
                            <div class="mt-5">
                                <span class="c-primary">活动编号：{{$data->game->id}}</span>
                            </div>
                            <div class="mt-5">
                                <span>展示时间：</span><span class="ml-5">{{explode(' ',$data->game->show_start_time)[0]}}
                                    -{{explode(' ',$data->game->show_end_time)[0]}}</span>
                            </div>
                            <div class="mt-5">
                                <span>参赛时间：</span><span
                                        class="ml-5">{{explode(' ',$data->game->join_start_time)[0]}}
                                    -{{explode(' ',$data->game->join_end_time)[0]}}</span>
                            </div>
                            <div class="mt-5">
                                <span>开始时间：</span><span class="ml-5">{{explode(' ',$data->game->game_start_time)[0]}}
                                    -{{explode(' ',$data->game->game_end_time)[0]}}</span>
                            </div>
                            <div class="mt-5">
                                <span>展示状态：</span><span
                                        class="ml-5 c-primary label label-primary">{{$data->game->show_status_str}}</span>
                            </div>
                            <div class="mt-5">
                                <span class="">参加状态：</span><span
                                        class="ml-5 label label-secondary">{{$data->game->join_status_str}}</span>
                            </div>
                            <div class="mt-5">
                                <span class="">活动状态：</span><span
                                        class="ml-5 label label-success">{{$data->game->game_status_str}}</span>
                            </div>
                            <div class="mt-5">
                                <span class="">结算状态：</span><span
                                        class="ml-5 label label-danger">{{$data->game->jiesuan_status_str}}</span>
                            </div>
                        </td>
                        <td>
                            <div class="mt-5">
                                <img src="{{ $data->user->avatar ? $data->user->avatar.'?imageView2/1/w/200/h/200/interlace/1/q/75|imageslim' : URL::asset('/img/default_headicon.png')}}"
                                     class="img-rect-30 radius-5">
                            </div>
                            <div class="mt-5">
                                <span>{{$data->user->nick_name}}({{$data->user->id}})</span>
                            </div>
                        </td>
                        <td>
                            <div>
                                参赛id：{{$data->join->id}}
                            </div>
                            <div class="mt-5">
                                关联订单：<span class="c-primary" style="cursor: pointer;"
                                           onclick="creatIframe('{{URL::asset('/admin/mryh/mryhJoinOrder/index')}}?search_word={{$data->join->trade_no}}', '订单信息-{{$data->join->trade_no}}');">{{$data->join->trade_no?$data->join->trade_no:'--'}}</span>
                            </div>
                            <div class="mt-5">
                                参赛时间：<span class="c-primary">{{$data->join->join_time}}</span>
                            </div>
                            <div class="mt-5">
                                参与天数：<span class="label label-primary">{{$data->join->join_day_num}}</span>
                            </div>
                            <div class="mt-5">
                                作品数目：<span class="label label-secondary">{{$data->join->work_num}}</span>
                            </div>
                            <div class="mt-5">
                                参与状态：<span class="c-primary">{{$data->join->game_status_str}}</span>
                            </div>
                            <div class="mt-5">
                                结算时间：<span
                                        class="c-primary">{{$data->join->jiesuan_time?$data->join->jiesuan_time:'--'}}</span>
                            </div>
                            <div class="mt-5">
                                结算状态：<span class="c-primary">{{$data->join->jiesuan_status_str}}</span>
                            </div>
                            <div class="mt-5">
                                结算金额：<span class="label label-primary">{{$data->join->jiesuan_price}}</span>
                            </div>
                        </td>
                        <td>
                            <div>
                                <img src="{{ $data->article->img.'?imageView2/1/w/80/h/40/interlace/1/q/75|imageslim'}}"/>
                            </div>
                            <div class="mt-5">
                                {{$data->article->name}}({{$data->article->id}})
                            </div>
                            <div class="mt-5">
                                上传时间：{{$data->article->created_at}}
                            </div>
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


    </script>
@endsection