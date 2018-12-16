@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb">优惠券管理 <a class="btn btn-success radius r btn-refresh"
                                     style="line-height:1.6em;margin-top:3px"
                                     title="刷新"
                                     onclick="location.replace('{{URL::asset('/admin/mryh/mryhCoupon/edit')}}?item={{$item}}&id={{isset($data)?$data->id:''}}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>

    <div class="page-container">
        <div class="btn-group text-c">
            <span class="btn {{$item==0?'btn-primary':'btn-default'}} radius"
                  onclick="selectItem(0)">基础信息</span>
            <span class="btn {{$item==1?'btn-primary':'btn-default'}} radius"
                  onclick="selectItem(1)">规则设置</span>
        </div>

        <div class="border-t mt-10 mb-20"></div>

        {{--通过item控制显示选项--}}
        @if($item=='0')
            @include('admin.mryh.mryhCoupon.item0')
        @endif
        @if($item=='1')
            @include('admin.mryh.mryhCoupon.item1')
        @endif

    </div>

@endsection

@section('script')
    <script type="text/javascript">

        //优惠券id
        var coupon_id = '{{$data->id}}';

        $(function () {

        });

        //选择项目
        function selectItem(item) {

            if (judgeIsAnyNullStr(coupon_id)) {
                layer.alert('必须配置优惠券基本信息后才可以进行其他配置');
                return;
            }
            var index = layer.load(2, {time: 10 * 1000}); //加载
            location.replace('{{URL::asset('/admin/mryh/mryhCoupon/edit')}}?id={{$data->id}}&item=' + item);
        }

    </script>
@endsection