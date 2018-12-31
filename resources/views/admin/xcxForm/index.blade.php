@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 小程序消息表单管理 <span
                class="c-gray en">&gt;</span> 小程序消息表单列表 <a class="btn btn-success radius r btn-refresh"
                                                           style="line-height:1.6em;margin-top:3px"
                                                           href="javascript:location.replace('{{URL::asset('/admin/xcxForm/index')}}');"
                                                           title="刷新"
                                                           onclick="location.replace('{{URL::asset('/admin/xcxForm/index')}}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>
    <div class="page-container">
        <div class="text-c">
            <form id="search_form" action="{{URL::asset('/admin/xcxForm/index')}}" method="post"
                  class="form-horizontal">
                {{csrf_field()}}
                <div class="Huiform text-r">
                    <span class="ml-5">用户id：</span>
                    <input id="user_id" name="user_id" type="text" class="input-text" style="width:100px"
                           placeholder="用户id" value="{{$con_arr['user_id']}}">
                    <span class="ml-5">是否使用：</span>
                    <span class="select-box" style="width: 100px;">
                        <select id="used_flag" name="used_flag" class="select">
                            <option value="">请选择</option>
                            @foreach(\App\Components\Utils::XCX_FORM_USED_FLAG_VAL as $key=>$value)
                                <option value="{{$key}}" {{$con_arr['used_flag']==strval($key)?'selected':''}}>{{$value}}</option>
                            @endforeach
                        </select>
                    </span>
                    <span class="ml-5">业务类型：</span>
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


        <div class="mt-20">
            <table class="table table-border table-bordered table-bg table-sort">
                <thead>
                <tr>
                    <th scope="col" colspan="15">小程序消息表单列表</th>
                </tr>
                <tr class="text-c">
                    {{--<th width="25"><input type="checkbox" name="" value=""></th>--}}
                    <th width="40">ID</th>
                    <th width="40">表单id</th>
                    <th width="40">业务名称</th>
                    <th width="120">用户信息</th>
                    <th width="40">f_table</th>
                    <th width="40">f_id</th>
                    <th width="40">总消息数</th>
                    <th width="40">已用消息数</th>
                    <th width="40">使用状态</th>
                    <th width="80">创建时间</th>
                </tr>
                </thead>
                <tbody>
                @foreach($datas as $data)
                    <tr class="text-c">
                        {{--<td><input type="checkbox" value="1" name=""></td>--}}
                        <td>{{$data->id}}</td>
                        <td>{{$data->form_id}}</td>
                        <td>{{$data->busi_name_str}}</td>
                        <td>{{$data->user->nick_name}}({{$data->user->id}})</td>
                        <td>{{$data->f_table}}</td>
                        <td>{{$data->f_id}}</td>
                        <td>{{$data->total_num}}</td>
                        <td>{{$data->used_num}}</td>
                        <td>{{$data->used_flag_str}}</td>
                        <td>{{$data->created_at}}</td>
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


    </script>
@endsection