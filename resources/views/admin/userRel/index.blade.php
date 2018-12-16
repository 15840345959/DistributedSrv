@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 用户管理 <span
                class="c-gray en">&gt;</span> 用户关联关系 <a class="btn btn-success radius r btn-refresh"
                                                        style="line-height:1.6em;margin-top:3px"
                                                        href="javascript:location.replace(location.href);" title="刷新"
                                                        onclick="location.replace('{{URL::asset('admin/userRel/index')}}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>
    <div class="page-container">
        <div class="text-c">
            <form id="search_form"  action="{{URL::asset('admin/userRel/index')}}" method="post" class="form-horizontal">
                {{csrf_field()}}
                <div class="Huiform text-r">
                    <span class="">邀请用户id</span>
                    <input id="a_user_id" name="a_user_id" type="text" class="input-text"
                           style="width:150px"
                           placeholder="邀请用户id" value="{{$con_arr['a_user_id']}}">
                    <span class="ml-10">被邀请用户id</span>
                    <input id="b_user_id" name="b_user_id" type="text" class="input-text"
                           style="width:150px"
                           placeholder="被邀请用户id" value="{{$con_arr['b_user_id']}}">
                    <span class="select-box" style="width: 150px;">
                        <select id="busi_name" name="busi_name" class="select">
                            <option value="">请选择</option>
                            @foreach(\App\Components\Utils::BUSI_NAME_VAL as $key=>$value)
                                <option value="{{$key}}" {{$con_arr['busi_name']==strval($key)?'selected':''}}>{{$value}}</option>
                            @endforeach
                        </select>
                    </span>
                    <span class="select-box" style="width: 120px;">
                        <select id="level" name="level" class="select">
                            <option value="">请选择</option>
                            @foreach(\App\Components\Utils::USER_REL_LEVEL_VAL as $key=>$value)
                                <option value="{{$key}}" {{$con_arr['level']==strval($key)?'selected':''}}>{{$value}}</option>
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
                <th scope="col" colspan="6">用户关联关系列表</th>
            </tr>
            <tr class="text-c">
                {{--<th width="25"><input type="checkbox" name="" value=""></th>--}}
                <th width="40">ID</th>
                <th width="80">邀请用户</th>
                <th width="80">被邀请用户</th>
                <th width="100">业务名称</th>
                <th width="100">邀请级别</th>
                <th width="100">邀请时间</th>
            </tr>
            </thead>
            <tbody>
            @foreach($datas as $data)
                <tr class="text-c">
                    {{--<td><input type="checkbox" value="1" name=""></td>--}}
                    <td>{{$data->id}}</td>
                    <td><span class="c-primary"
                              onclick="creatIframe('{{URL::asset('/admin/user/info')}}?id={{$data->a_user->id}}', '用户信息-{{$data->a_user->nick_name}}');">{{$data->a_user->nick_name}}
                            ({{$data->a_user->id}})</span></td>
                    <td><span class="c-primary"
                              onclick="creatIframe('{{URL::asset('/admin/user/info')}}?id={{$data->b_user->id}}', '用户信息-{{$data->b_user->nick_name}}');">{{$data->b_user->nick_name}}
                            ({{$data->b_user->id}})</span></td>
                    <td>{{$data->busi_name_str}}</td>
                    <td>{{$data->level_str}}</td>
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

        /*
         * 展示用户详细信息
         *
         * By TerryQi
         *
         * 2018-07-07
         *
         */
        function info(url, title) {
            creatIframe(url, title)
        }

    </script>
@endsection