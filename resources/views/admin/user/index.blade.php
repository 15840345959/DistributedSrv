@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 用户管理 <span
                class="c-gray en">&gt;</span> 用户列表 <a class="btn btn-success radius r btn-refresh"
                                                      style="line-height:1.6em;margin-top:3px"
                                                      href="javascript:location.replace(location.href);" title="刷新"
                                                      onclick="location.replace('{{URL::asset('admin/user/index')}}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>
    <div class="page-container">
        <div class="text-c">
            <form id="search_form" action="{{URL::asset('admin/user/index')}}" method="post" class="form-horizontal">
                {{csrf_field()}}
                <div class="Huiform text-r">
                    <input id="id" name="id" type="text" class="input-text" style="width:150px"
                           placeholder="用户id" value="{{$con_arr['id']?$con_arr['id']:''}}">
                    <input id="search_word" name="search_word" type="text" class="input-text" style="width:350px"
                           placeholder="用户昵称/姓名/手机号" value="{{$con_arr['search_word']?$con_arr['search_word']:''}}">
                    <span class="ml-5">用户类型：</span>
                    <span class="select-box" style="width: 150px;">
                        <select id="type" name="type" class="select">
                            <option value="">请选择</option>
                            @foreach(\App\Components\Utils::USER_TYPE_VAL as $key=>$value)
                                <option value="{{$key}}" {{$con_arr['type']==strval($key)?'selected':''}}>{{$value}}</option>
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
                <th scope="col" colspan="10">用户列表</th>
            </tr>
            <tr class="text-c">
                {{--<th width="25"><input type="checkbox" name="" value=""></th>--}}
                <th width="40">ID</th>
                <th width="50">头像</th>
                <th width="100">昵称</th>
                <th width="50">省份</th>
                <th width="50">城市</th>
                <th width="50">积分</th>
                <th width="130">加入时间</th>
                <th width="60">类型</th>
                <th width="50">状态</th>
                <th width="60">操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($datas as $data)
                <tr class="text-c">
                    {{--<td><input type="checkbox" value="1" name=""></td>--}}
                    <td>{{$data->id}}</td>
                    <td>
                        <img src="{{ $data->avatar ? $data->avatar.'?imageView2/1/w/200/h/200/interlace/1/q/75|imageslim' : URL::asset('/img/default_headicon.png')}}"
                             class="img-rect-30 radius-5">
                    </td>
                    <td>{{$data->nick_name}}</td>
                    <td>{{$data->province}}</td>
                    <td>{{$data->city}}</td>
                    <td>{{$data->score}}</td>
                    <td>{{$data->created_at}}</td>
                    <td>
                        <span class="c-primary">{{$data->type_str}}</span>
                    </td>
                    <td class="td-status">

                        @if($data->status=="1")
                            <span class="label label-success radius">正常</span>
                        @else
                            <span class="label label-default radius">冻结</span>
                        @endif
                    </td>
                    <td class="td-manage">
                        <div>
                            @if($data->status=="1")
                                <a style="text-decoration:none" onClick="stop(this,'{{$data->id}}')"
                                   href="javascript:;" class="c-primary"
                                   title="冻结">
                                    {{--<i class="Hui-iconfont">&#xe631;</i>--}}
                                    冻结
                                </a>
                            @else
                                <a style="text-decoration:none" onClick="start(this,'{{$data->id}}')"
                                   href="javascript:;" class="c-primary"
                                   title="启用">
                                    {{--<i class="Hui-iconfont">&#xe615;</i>--}}
                                    启用
                                </a>
                            @endif
                            <a title="详情" href="javascript:;"
                               onclick="info('{{URL::asset('admin/user/info')}}?id={{$data->id}})','用户详情-{{$data->nick_name}}')"
                               class="ml-5 c-primary" style="text-decoration:none">
                                详情
                            </a>
                        </div>
                        <div class="mt-5">
                            <a title="设置为管理员" href="javascript:;"
                               onclick="set_admin(this,'{{$data->id}}')"
                               class="c-primary" style="text-decoration:none">
                                管理员
                            </a>
                            <a title="设置为普通用户" href="javascript:;"
                               onclick="set_user(this,'{{$data->id}}')"
                               class="ml-5 c-primary" style="text-decoration:none">
                                用户
                            </a>
                        </div>
                        <div class="mt-5">
                            <a title="编辑信息" href="javascript:;"
                               onclick="edit('编辑信息','{{URL::asset('/admin/user/edit')}}?id={{$data->id}})',{{$data->id}})"
                               class="c-primary ml-5" style="text-decoration:none">
                                编辑信息
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


        /*管理员-冻结*/
        function stop(obj, id) {
            consoledebug.log("stop id:" + id);
            layer.confirm('确认要冻结吗？', function (index) {
                //此处请求后台程序，下方是成功后的前台处理
                var param = {
                    id: id,
                    status: 0,
                    _token: "{{ csrf_token() }}"
                }
                //从后台设置管理员状态
                user_setStatus('{{URL::asset('')}}', param, function (ret) {
                    if (ret.result == true) {
                        $("#search_form").submit();
                    }
                })
                layer.msg('已冻结', {icon: 5, time: 1000});
            });
        }

        /*管理员-启用*/
        function start(obj, id) {
            layer.confirm('确认要启用吗？', function (index) {
                //此处请求后台程序，下方是成功后的前台处理
                var param = {
                    id: id,
                    status: 1,
                    _token: "{{ csrf_token() }}"
                }
                //从后台设置管理员状态
                user_setStatus('{{URL::asset('')}}', param, function (ret) {
                    if (ret.result == true) {
                        $("#search_form").submit();
                    }
                })
                layer.msg('正常', {icon: 6, time: 1000});
            });
        }

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

        /*
         * 设置为管理员
         *
         * By TerryQi
         *
         * 2018-09-22
         *
         */
        function set_admin(obj, id) {
            layer.confirm('确认要设置为管理员吗？', function (index) {
                //此处请求后台程序，下方是成功后的前台处理
                var param = {
                    id: id,
                    type: 1,
                    _token: "{{ csrf_token() }}"
                }
                //从后台设置管理员状态
                user_setType('{{URL::asset('')}}', param, function (ret) {
                    if (ret.result == true) {
                        $("#search_form").submit();
                    }
                })
                layer.msg('已设为管理员', {icon: 6, time: 1000});
            });
        }

        /*
         * 设置为普通用户
         *
         * By TerryQi
         *
         * 2018-09-22
         *
         */
        function set_user(obj, id) {
            layer.confirm('确认要设置为普通用户吗？', function (index) {
                //此处请求后台程序，下方是成功后的前台处理
                var param = {
                    id: id,
                    type: 0,
                    _token: "{{ csrf_token() }}"
                }
                //从后台设置管理员状态
                user_setType('{{URL::asset('')}}', param, function (ret) {
                    if (ret.result == true) {
                        $("#search_form").submit();
                    }
                })
                layer.msg('已设为普通用户', {icon: 6, time: 1000});
            });
        }

        /*
         参数解释：
         title	标题
         url		请求的url
         id		需要操作的数据id
         w		弹出层宽度（缺省调默认值）
         h		弹出层高度（缺省调默认值）
         */
        /*广告图-增加*/
        function edit(title, url) {
            var index = layer.open({
                type: 2,
                title: title,
                content: url
            });
            layer.full(index);
        }

    </script>
@endsection