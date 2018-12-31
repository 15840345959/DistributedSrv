@extends('admin.layouts.app')

@section('content')

    <div class="text-c">
        <form id="search_form"
              action="{{URL::asset('/admin/vote/voteActivity/share/index')}}?activity_id={{$con_arr['activity_id']}}&user_id={{$con_arr['user_id']}}"
              method="post"
              class="form-horizontal">
            {{csrf_field()}}
            <div class="Huiform text-r mt-10">
                <span class="ml-5">开始日期：</span>
                <input id="start_at" name="start_at" type="date" class="input-text" style="width: 150px;"
                       value="{{$con_arr['start_at']}}" placeholder="请输入统计日期">
                <span class="ml-5">结束日期：</span>
                <input id="date_at" name="date_at" type="date" class="input-text" style="width: 150px;"
                       value="{{$con_arr['end_at']}}" placeholder="请输入统计日期">
                <button type="submit" class="btn btn-success" id="" name="">
                    <i class="Hui-iconfont"></i> 搜索
                </button>
            </div>
        </form>
    </div>


    <div class="page-container">
        <div class="mt-10">
            <table class="table table-border table-bordered table-bg table-sort">
                <thead>
                <tr>
                    <th scope="col" colspan="9">
                        分享明细列表
                        <span class="r">共有数据：<strong>{{$datas->count()}}</strong> 条</span>
                    </th>
                </tr>
                <tr class="text-c">
                    {{--<th width="25"><input type="checkbox" name="" value=""></th>--}}
                    <th width="40">编号</th>
                    <th width="100">录入人</th>
                    <th width="150">活动名称</th>
                    <th width="100">选手名称</th>
                    <th width="120">录入时间</th>
                    <th width="40">基本状态</th>
                    <th width="40">激活状态</th>
                    <th width="40">激活次数</th>
                </tr>
                </thead>
                <tbody>
                @foreach($datas as $data)
                    <tr class="text-c">
                        {{--<td><input type="checkbox" value="1" name=""></td>--}}
                        <td>{{$data->id}}</td>
                        <td>
                            {{--如果是用户，则要进入用户明细--}}
                            @if(isset($data->user))
                                <span class="c-primary" style="cursor: pointer;"
                                      onclick="userShareDetail('{{$data->user->nick_name}}','{{$data->user->id}}')">
                                {{isset($data->user)?$data->user->nick_name:'后台录入'}}({{$data->user_id}})
                                </span>
                            @else
                                <span>
                                {{isset($data->user)?$data->user->nick_name:'后台录入'}}({{$data->user_id}})
                                </span>
                            @endif
                        </td>
                        <td>{{$data->activity->name}}({{$data->activity_id}})</td>
                        <td>{{$data->name}}</td>
                        <td>
                            {{$data->created_at}}
                        </td>
                        <td>{{$data->status_str}}</td>
                        <td>{{$data->valid_status_str}}</td>
                        <td>{{$data->share_num}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">

        function userShareDetail(name, id) {
            console.log('id is : ' + id + " name:" + name)
            var index = layer.open({
                type: 2,
                title: `地推工作明细-` + name,
                content: '{{route('voteActivity.share.index')}}?user_id=' + id
            });
            layer.full(index);
        }

    </script>
@endsection