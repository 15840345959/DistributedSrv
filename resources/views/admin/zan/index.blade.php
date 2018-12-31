@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 点赞管理 <span
                class="c-gray en">&gt;</span> 点赞列表 <a class="btn btn-success radius r btn-refresh"
                                                      style="line-height:1.6em;margin-top:3px"
                                                      href="javascript:location.replace(location.href);" title="刷新"
                                                      onclick="location.replace('{{URL::asset('admin/zan/index')}}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>
    <div class="page-container">
        <div class="text-c">
            <form id="search_form" action="{{URL::asset('admin/zan/index')}}" method="post" class="form-horizontal">
                {{csrf_field()}}
                <div class="Huiform text-r">
                    <span class="">用户id：</span>
                    <input id="user_id" name="user_id" type="text" class="input-text" style="width:200px"
                           placeholder="用户id" value="{{$con_arr['user_id']?$con_arr['user_id']:''}}">
                    <span class="ml-5">父id：</span>
                    <input id="f_id" name="f_id" type="text" class="input-text" style="width:200px"
                           placeholder="父id" value="{{$con_arr['f_id']?$con_arr['f_id']:''}}">
                    <span class="ml-5">父表：</span>
                    <span class="select-box" style="width: 150px;">
                        <select id="f_table" name="f_table" class="select">
                            <option value="">请选择</option>
                            @foreach(\App\Components\Utils::F_TABLE_ARTICLE_VAL as $key=>$value)
                                <option value="{{$key}}" {{$con_arr['f_table']==strval($key)?'selected':''}}>{{$value}}</option>
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
                <th scope="col" colspan="5">点赞列表</th>
            </tr>
            <tr class="text-c">
                {{--<th width="25"><input type="checkbox" name="" value=""></th>--}}
                <th width="20">ID</th>
                <th width="50">用户</th>
                <th width="20">父表</th>
                <th width="240">父对象</th>
                <th width="40">操作时间</th>
            </tr>
            </thead>
            <tbody>
            @foreach($datas as $data)
                <tr class="text-c">
                    {{--<td><input type="checkbox" value="1" name=""></td>--}}
                    <td>{{$data->id}}</td>
                    <td>
                        <span class="c-primary">{{$data->user->nick_name}}({{$data->user->id}})</span>
                    </td>
                    <td>{{$data->f_table}}</td>
                    <td>
                        <span class="c-primary">
                            @if($data->f_table=='article')
                                @if(isset($data->article))
                                    {{$data->article->name}}({{$data->article->id}})
                                @else
                                    作品被删除({{$data->f_id}})
                                @endif
                            @endif
                        </span>
                    </td>
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