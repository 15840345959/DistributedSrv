@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb">活动管理 <a class="btn btn-success radius r btn-refresh"
                                    style="line-height:1.6em;margin-top:3px"
                                    title="刷新"
                                    onclick="location.replace('{{URL::asset('/admin/mryh/mryhGame/edit')}}?item={{$item}}&id={{isset($data)?$data->id:''}}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>

    <div class="page-container">
        <div class="btn-group text-c">
            <span class="btn {{$item==0?'btn-primary':'btn-default'}} radius"
                  onclick="selectItem(0)">活动设置</span>
            <span class="btn {{$item==1?'btn-primary':'btn-default'}} radius"
                  onclick="selectItem(1)">规则设置</span>
            <span class="btn {{$item==2?'btn-primary':'btn-default'}} radius"
                  onclick="selectItem(2)">内容设置</span>
            <span class="btn {{$item==3?'btn-primary':'btn-default'}} radius"
                  onclick="selectItem(3)">分享设置</span>
            <span class="btn {{$item==4?'btn-primary':'btn-default'}} radius"
                  onclick="selectItem(4)">数据管理</span>
        </div>

        <div class="border-t mt-10 mb-20"></div>

        {{--通过item控制显示选项--}}
        @if($item=='0')
            @include('admin.mryh.mryhGame.item0')
        @endif
        @if($item=='1')
            @include('admin.mryh.mryhGame.item1')
        @endif
        @if($item=='2')
            @include('admin.mryh.mryhGame.item2')
        @endif
        @if($item=='3')
            @include('admin.mryh.mryhGame.item3')
        @endif
        @if($item=='4')
            @include('admin.mryh.mryhGame.item4')
        @endif

    </div>

@endsection

@section('script')
    <script type="text/javascript">

        //活动id
        var game_id = '{{$data->id}}';

        $(function () {

        });

        //选择项目
        function selectItem(item) {

            if (judgeIsAnyNullStr(game_id)) {
                layer.alert('必须配置活动基本信息后才可以进行其他配置');
                return;
            }
            var index = layer.load(2, {time: 10 * 1000}); //加载
            location.replace('{{URL::asset('/admin/mryh/mryhGame/edit')}}?id={{$data->id}}&item=' + item);
        }

    </script>
@endsection