@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb">大赛管理 <a class="btn btn-success radius r btn-refresh"
                                    style="line-height:1.6em;margin-top:3px"
                                    title="刷新"
                                    onclick="location.replace('{{route('team.activity.edit')}}?item={{$item}}&id={{isset($data)?$data->id:''}}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>

    <div class="page-container">
        <div class="btn-group text-c">
            <span class="btn {{$item==0?'btn-primary':'btn-default'}} radius"
                  onclick="selectItem(0)">活动设置</span>
            <span class="btn {{$item==1?'btn-primary':'btn-default'}} radius"
                  onclick="selectItem(1)">规则设置</span>
            <span class="btn {{$item==2?'btn-primary':'btn-default'}} radius"
                  onclick="selectItem(2)">内容设置</span>
            {{--<span class="btn {{$item==3?'btn-primary':'btn-default'}} radius"--}}
                  {{--onclick="selectItem(3)">视频设置</span>--}}
            {{--<span class="btn {{$item==4?'btn-primary':'btn-default'}} radius"--}}
                  {{--onclick="selectItem(4)">参赛说明</span>--}}
            <span class="btn {{$item==5?'btn-primary':'btn-default'}} radius"
                  onclick="selectItem(5)">礼品设置</span>
            <span class="btn {{$item==6?'btn-primary':'btn-default'}} radius"
                  onclick="selectItem(6)">广告设置</span>
            <span class="btn {{$item==7?'btn-primary':'btn-default'}} radius"
                  onclick="selectItem(7)">分享设置</span>
            {{--<span class="btn {{$item==8?'btn-primary':'btn-default'}} radius"--}}
                  {{--onclick="selectItem(8)">负责人设置</span>--}}
            <span class="btn {{$item==9?'btn-primary':'btn-default'}} radius"
                  onclick="selectItem(9)">选手管理</span>
            {{--<span class="btn {{$item==10?'btn-primary':'btn-default'}} radius"--}}
                  {{--onclick="selectItem(10)">数据管理</span>--}}
            <span class="btn {{$item==11?'btn-primary':'btn-default'}} radius"
                  onclick="selectItem(11)">报名信息管理</span>
        </div>

        <div class="border-t mt-10 mb-20"></div>

        {{--通过item控制显示选项--}}
        @if($item=='0')
            @include('vote.team.activity.item0')
        @endif
        @if($item=='1')
            @include('vote.team.activity.item1')
        @endif
        @if($item=='2')
            @include('vote.team.activity.item2')
        @endif
        @if($item=='3')
            @include('vote.team.activity.item3')
        @endif
        @if($item=='4')
            @include('vote.team.activity.item4')
        @endif
        @if($item=='5')
            @include('vote.team.activity.item5')
        @endif
        @if($item=='6')
            @include('vote.team.activity.item6')
        @endif
        @if($item=='7')
            @include('vote.team.activity.item7')
        @endif
        @if($item=='8')
            @include('vote.team.activity.item8')
        @endif
        @if($item=='9')
            @include('vote.team.activity.item9')
        @endif
        @if($item=='10')
            @include('vote.team.activity.item10')
        @endif
        @if($item=='11')
            @include('vote.team.activity.item11')
        @endif
    </div>

@endsection

@section('script')
    <script type="text/javascript">

        //大赛id
        var activity_id = '{{$data->id}}';

        $(function () {

        });

        //选择项目
        function selectItem(item) {

            if (judgeIsAnyNullStr(activity_id)) {
                layer.alert('必须配置活动基本信息后才可以进行其他配置');
                return;
            }
            var index = layer.load(2, {time: 10 * 1000}); //加载
            location.replace('{{route('team.activity.edit')}}?id={{$data->id}}&item=' + item);
        }

    </script>
@endsection