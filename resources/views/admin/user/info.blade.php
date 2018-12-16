@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 用户详细信息 <span
                class="c-gray en">&gt;</span> 用户详情 <a class="btn btn-success radius r btn-refresh"
                                                      style="line-height:1.6em;margin-top:3px"
                                                      href="javascript:location.replace(location.href);" title="刷新"
                                                      onclick="location.replace('{{URL::asset('/admin/user/info')}}?id={{$data->id}}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>
    <div class="page-container">

        {{--用户基础信息--}}
        <div class="panel panel-primary mt-20">
            <div class="panel-header">基础信息</div>
            <div class="panel-body">

                <table class="table table-border table-bordered radius">
                    <tbody>
                    <tr>
                        <td rowspan="6" style="text-align: center;width: 120px;">
                            <img src="{{ $data->avatar ? $data->avatar.'?imageView2/1/w/200/h/200/interlace/1/q/75|imageslim' : URL::asset('/img/default_headicon.png')}}"
                                 style="width: 80px;height: 80px;">
                        </td>
                        <td>ID</td>
                        <td>{{isset($data->id)?$data->id:'--'}}</td>
                        <td>昵称</td>
                        <td>{{isset($data->nick_name)?$data->nick_name:'--'}}</td>
                        <td>姓名</td>
                        <td>{{isset($data->real_name)?$data->real_name:'--'}}</td>
                    </tr>
                    <tr>
                        <td>联系电话</td>
                        <td class="c-primary">{{isset($data->phonenum)?$data->phonenum:'--'}}</td>
                        <td>性别</td>
                        <td>
                            {{$data->gender_str}}
                        </td>
                        <td>注册时间</td>
                        <td>{{$data->created_at}}</td>
                    </tr>
                    <tr>
                        <td>状态</td>
                        <td class="c-primary">{{$data->status_str}}</td>
                        <td>积分</td>
                        <td>
                            <span class="c-primary">{{isset($data->score)?$data->score:'--'}}</span>
                        </td>
                        <td>签名</td>
                        <td>{{isset($data->sign)?$data->sign:'--'}}</td>
                    </tr>
                    <tr>
                        <td>国家</td>
                        <td>{{isset($data->country)?$data->country:'--'}}</td>
                        <td>省份</td>
                        <td>{{isset($data->province)?$data->province:'--'}}</td>
                        <td>城市</td>
                        <td>{{isset($data->city)?$data->city:'--'}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{--登录注册信息--}}
        <table class="table table-border table-bordered table-bg table-sort mt-10">
            <thead>
            <tr class="text-c">
                {{--<th width="25"><input type="checkbox" name="" value=""></th>--}}
                <th width="40">ID</th>
                <th width="100">账户类型</th>
                <th width="100">业务名称</th>
                <th width="200">ve_value1(用户openid/手机号)</th>
                <th width="200">ve_value2(用户unionid/密码)</th>
                <th width="100">加入时间</th>
            </tr>
            </thead>
            @foreach($logins as $login)
                <tr class="text-c">
                    {{--<td><input type="checkbox" value="1" name=""></td>--}}
                    <td>{{$login->id}}</td>
                    <td>{{$login->account_type_str}}</td>
                    <td>{{$login->busi_name_str}}</td>
                    <td>{{$login->ve_value1}}</td>
                    <td>{{$login->ve_value2}}</td>
                    <td>{{$login->created_at}}</td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection

@section('script')
    <script type="text/javascript">


        $(function () {

        });


    </script>
@endsection