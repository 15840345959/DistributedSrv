@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 投票明细管理 <span
                class="c-gray en">&gt;</span> 投票明细列表 <a class="btn btn-success radius r btn-refresh"
                                                        style="line-height:1.6em;margin-top:3px"
                                                        title="刷新"
                                                        onclick="location.replace('{{URL::asset('/admin/vote/voteRecord/index')}}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>
    <div class="page-container">

        <div class="text-c">
            <form id="search_form" action="{{URL::asset('/admin/vote/voteRecord/index')}}" method="post"
                  class="form-horizontal">
                {{csrf_field()}}
                <div class="Huiform text-r">
                    <span>选手id</span>
                    <input id="vote_user_id" name="vote_user_id" type="text" class="input-text"
                           style="width:250px"
                           placeholder="选手id" value="{{$con_arr['vote_user_id']}}">
                    <span class="ml-10">投票人id</span>
                    <input id="user_id" name="user_id" type="text" class="input-text" style="width:250px"
                           placeholder="投票人id" value="{{$con_arr['user_id']}}">
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
                    <th scope="col" colspan="6">投票明细列表<span
                                class="r">共有数据：<strong>{{$datas->total()}}</strong> 条</span></th>
                </tr>
                <tr class="text-c">
                    {{--<th width="25"><input type="checkbox" name="" value=""></th>--}}
                    <th width="50">ID</th>
                    <th width="100">选手信息</th>
                    <th width="100">投票人信息</th>
                    <th width="50">投票类型</th>
                    <th width="50">票数</th>
                    <th width="50">投票时间</th>
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
                                  onclick="creatIframe('{{URL::asset('/admin/vote/voteUser/index')}}?vote_user_id={{$data->vote_user->id}}', '选手信息-{{$data->vote_user->name}}');">
                                {{$data->vote_user->name}}({{$data->vote_user->id}})
                            </span>
                        </td>
                        <td>
                            <span class="c-primary" style="cursor: pointer;"
                                  onclick="creatIframe('{{URL::asset('/admin/user/info')}}?id={{$data->user->id}}', '用户信息-{{$data->user->nick_name}}');">{{$data->user->nick_name}}
                                ({{$data->user->id}})</span>
                        </td>
                        <td>
                            <span>{{$data->vote_type_str}}</span>
                        </td>
                        <td>
                            <span>{{$data->vote_num}}</span>
                        </td>
                        <td>
                            <span>{{$data->created_at}}</span>
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