@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 参与信息 <span
                class="c-gray en">&gt;</span> 活动清分明细列表 <a class="btn btn-success radius r btn-refresh"
                                                          style="line-height:1.6em;margin-top:3px"
                                                          title="刷新"
                                                          onclick="location.replace('{{URL::asset('/admin/mryh/mryhComputePrize/index')}}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>
    <div class="page-container">

        <div class="text-c">
            <form id="search_form" action="{{URL::asset('/admin/mryh/mryhComputePrize/index')}}" method="post"
                  class="form-horizontal">
                {{csrf_field()}}
                <div class="Huiform text-r">
                    <span>活动id</span>
                    <input id="game_id" name="game_id" type="text" class="input-text ml-5" style="width:150px"
                           value="{{$con_arr['game_id']==null?'':$con_arr['game_id']}}">
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
                    <th scope="col" colspan="9">活动清分明细列表 <span
                                class="r">共有数据：<strong>{{$datas->total()}}</strong> 条</span></th>
                </tr>
                <tr class="text-c">
                    {{--<th width="25"><input type="checkbox" name="" value=""></th>--}}
                    <th width="20">ID</th>
                    <th width="80">活动信息</th>
                    <th width="60">参赛总数</th>
                    <th width="60">成功数</th>
                    <th width="60">失败数</th>
                    <th width="60">平均奖励</th>
                    <th width="50">完成清分数</th>
                    <th width="60">清分状态</th>
                    <th width="40">执行时间</th>
                </tr>
                </thead>
                <tbody>
                @foreach($datas as $data)
                    <tr class="text-c">
                        <td>
                            <span>{{$data->id}}</span>
                        </td>
                        <td>
                            <span class="c-primary" style="cursor: pointer;"
                                  onclick="creatIframe('{{URL::asset('/admin/mryh/mryhGame/index')}}?id={{$data->game->id}}', '活动详情-{{$data->game->name}}');">
                                {{$data->game->name}}({{$data->game->id}})/{{$data->game->join_price}}元</span>
                        </td>
                        <td>
                            {{$data->game->join_num}}
                        </td>
                        <td>
                            <span class="c-primary">{{$data->success_num}}</span>
                        </td>
                        <td>
                            <span class="c-danger">{{$data->fail_num}}</span>
                        </td>
                        <td>
                            <span class="c-success">{{$data->ave_prize}}元</span>
                        </td>
                        <td>
                            {{$data->compute_num}}
                        </td>
                        <td>
                            {{$data->compute_status_str}}
                        </td>
                        <td>
                            {{$data->created_at}}
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