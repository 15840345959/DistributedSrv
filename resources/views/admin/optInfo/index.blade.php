@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 操作值管理 <span
                class="c-gray en">&gt;</span> 操作值列表 <a class="btn btn-success radius r btn-refresh"
                                                       style="line-height:1.6em;margin-top:3px"
                                                       href="javascript:location.replace(location.href);" title="刷新"
                                                       onclick="location.replace('{{ route('optInfo.index') }}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>
    <div class="page-container">

        <div class="text-c">
            <form id="search_form" action="{{ route('optInfo.index') }}" method="post" class="form-horizontal">
                {{csrf_field()}}
                <div class="Huiform text-r">
                     <span class="select-box" style="width: 250px;">
                        <select class="select" name="f_table" id="f_table" size="1">
                            <option value="" {{$con_arr['f_table']==null?'selected':''}}>全部类别</option>
                            <option value="order" {{$con_arr['f_table']=='order'?'selected':''}}>订单类别</option>
                            <option value="logistics" {{$con_arr['f_table']=='activity'?'selected':''}}>活动类别</option>
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
                 <a href="javascript:;" onclick="optInfo_edit('添加操作值','{{ route('optInfo.edit') }}')"
                    class="btn btn-primary radius">
                     <i class="Hui-iconfont">&#xe600;</i> 添加操作值
                 </a>
            </span>
        </div>
        <table class="table table-border table-bordered table-bg table-sort mt-20">
            <thead>
            <tr>
                <th scope="col" colspan="5">操作值列表</th>
            </tr>
            <tr class="text-c">
                <th width="40">ID</th>
                <th width="100">操作分类（在所属业务中可见该操作）</th>
                <th width="100">操作动作（用于记录操作动作）</th>
                <th width="50">操作值</th>
                <th width="40">操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($datas as $data)
                <tr class="text-c">
                    <td>{{$data->id}}</td>
                    <td>
                        <span class="c-primary">{{$data->f_table_str}} </span>
                    </td>
                    <td>{{$data->name}}</td>
                    <td>{{isset($data->value)?$data->value:'预留'}}</td>
                    <td class="td-manage">
                        <a title="编辑" href="javascript:;"
                           onclick="optInfo_edit('操作值编辑','{{ route('optInfo.edit', ['id' => $data->id]) }}')"
                           class="ml-5" style="text-decoration:none">
                            <i class="Hui-iconfont">&#xe6df;</i>
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

        /*操作值-编辑*/
        function optInfo_edit(title, url, id) {
            console.log("optInfo_edit url:" + url);
            var index = layer.open({
                type: 2,
                area: ['550px', '350px'],
                fixed: false,
                maxmin: true,
                title: title,
                content: url
            });
        }

    </script>
@endsection