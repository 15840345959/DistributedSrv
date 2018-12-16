@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> ISART服务号信息 <span
                class="c-gray en">&gt;</span> 基础信息 <a class="btn btn-success radius r btn-refresh"
                                                      style="line-height:1.6em;margin-top:3px"
                                                      href="javascript:location.replace(location.href);" title="刷新"
                                                      onclick="location.replace('{{URL::asset('/admin/base/isart/info')}}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>
    <div class="page-container">

        {{--订阅号二维码--}}
        <div class="row ml-20 mt-20">
            <div class="">
                <img src="{{URL::asset('/img/isart_ceshi_ewm.png')}}" style="width: 150px;height: 150px;">
            </div>
            <div class="mt-15">
                <span class="c-999">由于订阅号没有js安全域名权限，因此调整为测试机的二维码，用于测试业务，测试环境中连接至该公众号</span>
            </div>
        </div>

        {{--订阅号二维码--}}
        <div class="row ml-20 mt-20">
            <div class="">
                <img src="{{URL::asset('/img/wjh_fwh_ewm.jpg')}}" style="width: 150px;height: 150px;">
            </div>
            <div class="mt-15">
                <span class="c-999">万景海服务号，目前使用该公众号进行业务测试</span>
            </div>
        </div>

        {{--生产服务号二维码--}}
        <div class="row ml-20 mt-20">
            <div class="">
                <img src="{{URL::asset('/img/isart_fwh_ewm.jpg')}}" style="width: 150px;height: 150px;">
            </div>
            <div class="mt-15">
                <span class="c-999">ISART生产服务号二维码！！！</span>
            </div>
        </div>

        {{--艺术榜小程序码--}}
        <div class="row ml-20 mt-20">
            <div class="">
                <img src="{{URL::asset('/img/ysb_xcx_ewm.jpg')}}" style="width: 150px;height: 150px;">
            </div>
            <div class="mt-15">
                <span class="c-999">艺术榜小程序码！！！</span>
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