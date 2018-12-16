@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 商品类型管理 <span
                class="c-gray en">&gt;</span> 商品类型列表 <a class="btn btn-success radius r btn-refresh"
                                                        style="line-height:1.6em;margin-top:3px"
                                                        href="javascript:location.replace(location.href);" title="刷新"
                                                        onclick="location.replace('{{URL::asset('/admin/goodsType/index')}}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>
    <div class="page-container">

        <div class="text-c">
            <form id="search_form"  action="{{URL::asset('/admin/goodsType/index')}}" method="post" class="form-horizontal">
                {{csrf_field()}}
                <div class="Huiform text-r">
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
            <span class="l">
                 <a href="javascript:;" onclick="goodsType_edit('添加商品类型','{{ route('goodsType.edit') }}')"
                    class="btn btn-primary radius">
                     <i class="Hui-iconfont">&#xe600;</i> 添加商品类型
                 </a>
            </span>
        </div>
        <table class="table table-border table-bordered table-bg table-sort mt-20">
            <thead>
            <tr>
                <th scope="col" colspan="7">商品类型列表</th>
            </tr>
            <tr class="text-c">
                <th width="40">ID</th>
                <th width="50">图标</th>
                <th width="50">名称</th>
                <th width="50">所属业务</th>
                <th width="50">状态</th>
                <th width="50">排序</th>
                <th width="40">操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($datas as $data)
                <tr class="text-c">
                    <td>{{$data->id}}</td>
                    <td>
                        <img src="{{ $data->img}}?imageView2/1/w/200/h/200/interlace/1/q/75|imageslim"
                             class="img-rect-30 radius-5">
                    </td>
                    <td>{{$data->name}}</td>
                    <td>{{$data->busi_name_str}}</td>
                    <td>
                       <span class="ml-5 label label-secondary">
                           {{$data->status_str}}
                       </span>
                    </td>
                    <td>{{$data->seq}}</td>
                    <td class="td-manage">
                        <a style="text-decoration:none" onClick="start(this,'{{$data->id}}')"
                           href="javascript:;" class="c-primary"
                           title="生效">
                            生效
                        </a>
                        <a style="text-decoration:none" onClick="stop(this,'{{$data->id}}')"
                           href="javascript:;" class="ml-5 c-primary"
                           title="冻结">
                            冻结
                        </a>
                        <a title="编辑" href="javascript:;"
                           onclick="goodsType_edit('商品类型编辑','{{ route('goodsType.edit', ['id' => $data->id]) }}')"
                           class="ml-5 c-primary" style="text-decoration:none">
                            编辑
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="mt-20">
            {{ $datas->links() }}
        </div>
    </div>

@endsection

@section('script')
    <script type="text/javascript">

        $(function () {

        });

        /*商品类型-编辑*/
        function goodsType_edit(title, url, id) {
            console.log("goodsType_edit url:" + url);
            var index = layer.open({
                type: 2,
                area: ['850px', '550px'],
                fixed: false,
                maxmin: true,
                title: title,
                content: url
            });
        }

        /*商品类型-隐藏*/
        function stop(obj, id) {
            console.log("stop id:" + id);
            layer.confirm('确认要隐藏吗？', function (index) {
                //此处请求后台程序，下方是成功后的前台处理
                var param = {
                    id: id,
                    status: 0,
                    _token: "{{ csrf_token() }}"
                }
                //从后台设置商品状态
                goodsType_setStatus('{{URL::asset('')}}', param, function (ret) {
                    if (ret.result == true) {
                        $("#search_form").submit();
                    }
                })
//                <i class="Hui-iconfont">&#xe631;</i>
                layer.msg('已隐藏', {icon: 5, time: 1000});
            });
        }

        /*商品类型-显示*/
        function start(obj, id) {
            layer.confirm('确认要显示吗？', function (index) {
                //此处请求后台程序，下方是成功后的前台处理
                var param = {
                    id: id,
                    status: 1,
                    _token: "{{ csrf_token() }}"
                }
                //从后台设置商品状态
                goodsType_setStatus('{{URL::asset('')}}', param, function (ret) {
                    if (ret.result == true) {
                        $("#search_form").submit();
                    }
                })
                layer.msg('已显示', {icon: 6, time: 1000});
            });
        }

    </script>
@endsection