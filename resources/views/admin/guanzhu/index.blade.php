@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 关注明细管理 <span
                class="c-gray en">&gt;</span> 关注明细列表 <a class="btn btn-success radius r btn-refresh"
                                                        style="line-height:1.6em;margin-top:3px"
                                                        href="javascript:location.replace(location.href);" title="刷新"
                                                        onclick="location.replace('{{URL::asset('admin/guanzhu/index')}}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>
    <div class="page-container">
        <div class="text-c">
            <form id="search_form"  action="{{URL::asset('admin/guanzhu/index')}}" method="post" class="form-horizontal">
                {{csrf_field()}}
                <div class="Huiform text-r">
                    <span>被关注用户id：</span>
                    <input id="gz_user_id" name="gz_user_id" type="text" class="input-text" style="width:150px"
                           placeholder="被关注用户id" value="{{$con_arr['gz_user_id']}}">
                    <span class="ml-5">粉丝用户id：</span>
                    <input id="fan_user_id" name="fan_user_id" type="text" class="input-text" style="width:150px"
                           placeholder="粉丝用户id" value="{{$con_arr['fan_user_id']}}">
                    <span class="ml-5">归属业务：</span>
                    <span class="select-box" style="width: 150px;">
                        <select id="busi_name" name="busi_name" class="select">
                            <option value="">请选择</option>
                            @foreach(\App\Components\Utils::BUSI_NAME_VAL as $key=>$value)
                                <option value="{{$key}}" {{$con_arr['busi_name']==strval($key)?'selected':''}}>{{$value}}</option>
                            @endforeach
                        </select>
                    </span>
                    <button type="submit" class="btn btn-success" id="" name="">
                        <i class="Hui-iconfont">&#xe665;</i> 搜索
                    </button>
                </div>
            </form>
        </div>
        <div class="cl pd-5 bg-1 bk-gray mt-20">
            <span class="r">共有数据：<strong>{{$datas->total()}}</strong> 条</span>
        </div>
        <table class="table table-border table-bordered table-bg table-sort mt-10">
            <thead>
            <tr>
                <th scope="col" colspan="5">关注明细列表</th>
            </tr>
            <tr class="text-c">
                {{--<th width="25"><input type="checkbox" name="" value=""></th>--}}
                <th width="40">ID</th>
                <th width="50">被关注用户</th>
                <th width="100">粉丝用户</th>
                <th width="100">业务名称</th>
                <th width="130">关注时间</th>
            </tr>
            </thead>
            <tbody>
            @foreach($datas as $data)
                <tr class="text-c">
                    {{--<td><input type="checkbox" value="1" name=""></td>--}}
                    <td>{{$data->id}}</td>
                    <td>{{$data->gz_user->nick_name}}({{$data->gz_user->id}})</td>
                    <td>{{$data->fan_user->nick_name}}({{$data->fan_user->id}})</td>
                    <td>{{$data->busi_name_str}}</td>
                    <td>{{$data->created_at}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="mt-20">
            {{ $datas->appends($con_arr)->links() }}
        </div>
    </div>

@endsection

@section('script')
    <script type="text/javascript">


    </script>
@endsection