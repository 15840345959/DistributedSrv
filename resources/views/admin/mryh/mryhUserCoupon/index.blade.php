@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 参与信息 <span
                class="c-gray en">&gt;</span> 优惠券派发明细列表 <a class="btn btn-success radius r btn-refresh"
                                                           style="line-height:1.6em;margin-top:3px"
                                                           title="刷新"
                                                           onclick="location.replace('{{URL::asset('/admin/mryh/mryhUserCoupon/index')}}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>
    <div class="page-container">

        <div class="text-c">
            <form id="search_form"  action="{{URL::asset('/admin/mryh/mryhUserCoupon/index')}}" method="post" class="form-horizontal">
                {{csrf_field()}}
                <div class="Huiform text-r">
                    <span>用户id</span>
                    <input id="user_id" name="user_id" type="text" class="input-text"
                           style="width:100px"
                           placeholder="用户id" value="{{$con_arr['user_id']}}">
                    <span class="ml-10">优惠券id</span>
                    <input id="coupon_id" name="coupon_id" type="text" class="input-text"
                           style="width:100px"
                           placeholder="活动id" value="{{$con_arr['coupon_id']}}">
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
                    <th scope="col" colspan="6">优惠券派发明细列表
                        <span class="r">共有数据：<strong>{{$datas->total()}}</strong> 条</span></th>
                </tr>
                <tr class="text-c">
                    {{--<th width="25"><input type="checkbox" name="" value=""></th>--}}
                    <th width="50">ID</th>
                    <th width="100">优惠券</th>
                    <th width="100">用户</th>
                    <th width="150">派发时间</th>
                    <th width="150">使用状态</th>
                    <th width="150">到期时间</th>
                </tr>
                </thead>
                <tbody>
                @foreach($datas as $data)
                    <tr class="text-c">
                        <td>
                            <span>{{$data->id}}</span>
                        </td>
                        <td>
                            <span class="c-primary"
                                  onclick="creatIframe('{{URL::asset('/admin/mryh/mryhCoupon/edit')}}?id={{$data->coupon->id}}', '优惠券信息-{{$data->coupon->name}}');">{{$data->coupon->name}}
                                ({{$data->coupon->id}})</span>
                        </td>
                        <td>
                            <span class="c-primary"
                                  onclick="creatIframe('{{URL::asset('/admin/user/info')}}?id={{$data->user->id}}', '选手信息-{{$data->user->nick_name}}');">{{$data->user->nick_name}}
                                ({{$data->user->id}})</span>
                        </td>
                        <td>
                            {{$data->alloc_time}}
                        </td>
                        <td>
                            {{$data->used_status_str}}
                        </td>
                        <td>
                            {{$data->end_time}}
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