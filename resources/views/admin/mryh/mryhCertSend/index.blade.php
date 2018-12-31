@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 证书发放管理 <span
                class="c-gray en">&gt;</span> 证书发放列表 <a class="btn btn-success radius r btn-refresh"
                                                        style="line-height:1.6em;margin-top:3px"
                                                        title="刷新"
                                                        onclick="location.replace('{{URL::asset('/admin/mryh/mryhCertSend/index')}}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>
    <div class="page-container">

        <div class="mt-20">
            <table class="table table-border table-bordered table-bg table-sort">
                <thead>
                <tr>
                    <th scope="col" colspan="9">证书发放列表<span
                                class="r">共有数据：<strong>{{$datas->total()}}</strong> 条</span></th>
                </tr>
                <tr class="text-c">
                    {{--<th width="25"><input type="checkbox" name="" value=""></th>--}}
                    <th width="40">ID</th>
                    <th width="40">参赛ID</th>
                    <th width="100">大赛名称</th>
                    <th width="100">参赛人</th>
                    <th width="60">openid</th>
                    <th width="150">证书路径</th>
                    <th width="100">状态</th>
                    <th width="150">发放时间</th>
                </tr>
                </thead>
                <tbody>
                @foreach($datas as $data)
                    <tr class="text-c">
                        {{--<td><input type="checkbox" value="1" name=""></td>--}}
                        <td>{{$data->id}}</td>
                        <td>
                            {{$data->join_id}}
                        </td>
                        <td>
                            {{$data->join->game->name}}
                        </td>
                        <td>{{$data->join->user->nick_name}}</td>
                        <td>
                            {{$data->to_openid}}
                        </td>
                        <td>
                            {{$data->cert_path}}
                        </td>
                        <td>
                            {{$data->status_str}}
                        </td>
                        <td>{{$data->created_at}}</td>
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