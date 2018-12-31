@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 大赛管理 <span
                class="c-gray en">&gt;</span> 获奖列表 <a class="btn btn-success radius r btn-refresh"
                                                       style="line-height:1.6em;margin-top:3px"
                                                       title="刷新"
                                                       onclick="location.replace('{{route('voteActivity.prizeStatements.web', ['id' => $id])}}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>



    <div class="page-container">

        <div class="cl pd-5 bg-1 bk-gray mt-20">
        <span class="l">
             <a href="javascript:;" onclick="clickExport('{{$id}}')" class="btn btn-primary radius">
                 导出表格
             </a>
        </span>
        </div>

        <div class="mt-20">
            <table class="table table-border table-bordered table-bg table-sort">
                <thead>
                <tr>
                    <th scope="col" colspan="9">
                        获奖列表
                        {{--<span class="r">共有数据：<strong>{{$datas->total()}}</strong> 条</span>--}}
                    </th>
                </tr>
                <tr class="text-c">
                    {{--<th width="25"><input type="checkbox" name="" value=""></th>--}}
                    <th width="40">编号</th>
                    <th width="150">姓名</th>
                    <th width="120">奖项</th>
                    <th width="200">发证日期</th>
                </tr>
                </thead>
                <tbody>
                @foreach($datas as $data)
                    <tr class="text-c">
                        {{--<td><input type="checkbox" value="1" name=""></td>--}}
                        <td>{{$data['code']}}</td>
                        <td>{{$data['name']}}</td>
                        <td>{{$data['prize']}}</td>
                        <td>{{$data['time']}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">

        function clickExport (id) {
            window.location.href = '{{route('voteActivity.prizeStatements.excel')}}?id=' + id + '&_token=' + '{{csrf_token()}}'
        }


    </script>
@endsection