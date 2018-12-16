@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 评论管理 <span
                class="c-gray en">&gt;</span> 评论列表 <a class="btn btn-success radius r btn-refresh"
                                                      style="line-height:1.6em;margin-top:3px"
                                                      href="javascript:location.replace(location.href);" title="刷新"
                                                      onclick="location.replace('{{URL::asset('admin/comment/index')}}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>
    <div class="page-container">
        <div class="text-c">
            <form id="search_form" action="{{URL::asset('admin/comment/index')}}" method="post" class="form-horizontal">
                {{csrf_field()}}
                <div class="Huiform text-r">
                    <span class="">用户id：</span>
                    <input id="user_id" name="user_id" type="text" class="input-text" style="width:100px"
                           placeholder="用户id" value="{{$con_arr['user_id']?$con_arr['user_id']:''}}">
                    <span class="ml-5">父id：</span>
                    <input id="f_id" name="f_id" type="text" class="input-text" style="width:100px"
                           placeholder="父id" value="{{$con_arr['f_id']?$con_arr['f_id']:''}}">
                    <span class="ml-5">父表：</span>
                    <span class="select-box" style="width: 100px;">
                        <select id="f_table" name="f_table" class="select">
                            <option value="">请选择</option>
                            @foreach(\App\Components\Utils::F_TABLE_ARTICLE_VAL as $key=>$value)
                                <option value="{{$key}}" {{$con_arr['f_table']==strval($key)?'selected':''}}>{{$value}}</option>
                            @endforeach
                        </select>
                    </span>
                    <span class="select-box" style="width: 100px;">
                        <select id="audit_status" name="audit_status" class="select">
                            <option value="">请选择</option>
                            @foreach(\App\Components\Utils::COMMENT_AUDIT_STATUS_VAL as $key=>$value)
                                <option value="{{$key}}" {{$con_arr['audit_status']==strval($key)?'selected':''}}>{{$value}}</option>
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
                <th scope="col" colspan="8">评论列表</th>
            </tr>
            <tr class="text-c">
                {{--<th width="25"><input type="checkbox" name="" value=""></th>--}}
                <th width="20">ID</th>
                <th width="50">用户</th>
                <th width="20">父表</th>
                <th width="100">父对象</th>
                <th width="140">评论内容</th>
                <th width="20">状态</th>
                <th width="20">操作时间</th>
                <th width="30">操作</th>
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
                                {{isset($data->article)?$data->article->name.'('.$data->article->id.')':'作品已经删除-作品id('.$data->f_id.')'}}
                            @endif
                        </span>
                    </td>
                    <td>{{$data->content}}</td>
                    <td>
                        <div class="">
                            <span class="ml-5 label label-primary">{{$data->recomm_flag_str}}</span>
                        </div>
                        <div class="mt-5">
                            <span class="ml-5 label label-secondary">{{$data->audit_status_str}}</span>
                        </div>
                    </td>
                    <td>{{$data->created_at}}</td>
                    <td>
                        <div class="mt-5">
                            <a style="text-decoration:none" onClick="recomm(this,'{{$data->id}}')"
                               href="javascript:;" class="c-primary"
                               title="推荐">
                                推荐
                            </a>
                            <a style="text-decoration:none" onClick="not_recomm(this,'{{$data->id}}')"
                               href="javascript:;" class="ml-5 c-primary"
                               title="不推荐">
                                不推荐
                            </a>
                        </div>
                        <div class="mt-5">
                            <a style="text-decoration:none" onClick="audit_pass(this,'{{$data->id}}')"
                               href="javascript:;" class="ml-5 c-primary"
                               title="审核通过">
                                审核通过
                            </a>
                        </div>
                        <div class="mt-5">
                            <a style="text-decoration:none" onClick="audit_reject(this,'{{$data->id}}')"
                               href="javascript:;" class="ml-5 c-primary"
                               title="审核驳回">
                                审核驳回
                            </a>
                        </div>
                    </td>
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

        //推荐
        function recomm(obj, id) {
            layer.confirm('确认要设置推荐吗？', function (index) {
                //此处请求后台程序，下方是成功后的前台处理
                var param = {
                    id: id,
                    recomm_flag: 1,
                    _token: "{{ csrf_token() }}"
                }
                //从后台设置作品状态
                comment_setStatus('{{URL::asset('')}}', param, function (ret) {
                    if (ret.result == true) {
                        $("#search_form").submit();
                    }
                })
                layer.msg('已经推荐', {icon: 6, time: 1000});
            });
        }

        //推荐
        function not_recomm(obj, id) {
            layer.confirm('确认要取消推荐吗？', function (index) {
                //此处请求后台程序，下方是成功后的前台处理
                var param = {
                    id: id,
                    recomm_flag: 0,
                    _token: "{{ csrf_token() }}"
                }
                //从后台设置作品状态
                comment_setStatus('{{URL::asset('')}}', param, function (ret) {
                    if (ret.result == true) {
                        $("#search_form").submit();
                    }
                })
                layer.msg('取消推荐', {icon: 6, time: 1000});
            });
        }

        /*
         * 审核通过
         *
         * By TerryQi
         *
         * 2018-09-22
         */
        function audit_pass(obj, id) {
            layer.confirm('确认要审核通过吗？', function (index) {
                //此处请求后台程序，下方是成功后的前台处理
                var param = {
                    id: id,
                    audit_status: 1,
                    _token: "{{ csrf_token() }}"
                }
                //从后台设置作品状态
                comment_setStatus('{{URL::asset('')}}', param, function (ret) {
                    if (ret.result == true) {
                        $("#search_form").submit();
                    }
                })
                layer.msg('已经审核通过', {icon: 6, time: 1000});
            });
        }

        /*
         * 审核驳回
         *
         * By TerryQi
         *
         * 2018-09-22
         */
        function audit_reject(obj, id) {
            layer.confirm('确认要审核驳回吗？', function (index) {
                //此处请求后台程序，下方是成功后的前台处理
                var param = {
                    id: id,
                    audit_status: 2,
                    _token: "{{ csrf_token() }}"
                }
                //从后台设置作品状态
                comment_setStatus('{{URL::asset('')}}', param, function (ret) {
                    if (ret.result == true) {
                        $("#search_form").submit();
                    }
                })
                layer.msg('已经审核驳回', {icon: 6, time: 1000});
            });
        }


    </script>
@endsection