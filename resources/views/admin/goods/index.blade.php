@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 商品管理 <span
                class="c-gray en">&gt;</span> 商品列表 <a class="btn btn-success radius r btn-refresh"
                                                      style="line-height:1.6em;margin-top:3px"
                                                      href="javascript:location.replace('{{URL::asset('/admin/goods/index')}}');"
                                                      title="刷新"
                                                      onclick="location.replace('{{URL::asset('/admin/goods/index')}}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>
    <div class="page-container">
        <div class="text-c">
            <form  id="search_form" action="{{URL::asset('/admin/goods/index')}}" method="post" class="form-horizontal">
                {{csrf_field()}}
                <div class="Huiform text-r">
                    <input id="search_word" name="search_word" type="text" class="input-text" style="width:200px"
                           placeholder="根据商品标题搜索" value="{{$con_arr['search_word']}}">
                    <span class="ml-5">是否推荐：</span>
                    <span class="select-box" style="width: 100px;">
                        <select id="recomm_flag" name="recomm_flag" class="select">
                            <option value="">请选择</option>
                            @foreach(\App\Components\Utils::ARTICLE_RECOMM_FLAG_VAL as $key=>$value)
                                <option value="{{$key}}" {{$con_arr['recomm_flag']==strval($key)?'selected':''}}>{{$value}}</option>
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

        <div class="cl pd-5 bg-1 bk-gray mt-20">
            <span class="l">
                 <a href="javascript:;" onclick="edit('添加商品','{{URL::asset('/admin/goods/edit')}}')"
                    class="btn btn-primary radius">
                     <i class="Hui-iconfont">&#xe600;</i> 添加商品
                 </a>
            </span>
            <span class="r">共有数据：<strong>{{$datas->total()}}</strong> 条</span>
        </div>

        <div class="mt-20">
            <table class="table table-border table-bordered table-bg table-sort">
                <thead>
                <tr>
                    <th scope="col" colspan="8">商品列表</th>
                </tr>
                <tr class="text-c">
                    {{--<th width="25"><input type="checkbox" name="" value=""></th>--}}
                    <th width="40">ID</th>
                    <th width="120">图片</th>
                    <th width="80">商品设置</th>
                    <th width="80">业务数据</th>
                    <th width="60">状态</th>
                    <th width="60">操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($datas as $data)
                    <tr class="text-c">
                        {{--<td><input type="checkbox" value="1" name=""></td>--}}
                        <td>{{$data->id}}</td>
                        <td>
                            <div>
                                <img src="{{ $data->img.'?imageView2/1/w/80/h/40/interlace/1/q/75|imageslim'}}"/>
                            </div>
                            <div class="mt-5">
                                <span class="c-primary">{{$data->name}}</span>
                            </div>
                            <div class="mt-5">
                                @if(isset($data->editor))
                                    <span class="c-primary">推荐小编：{{$data->editor->nick_name}}</span>
                                @endif
                            </div>
                            <div class="mt-5">
                                <span>{{$data->created_at}}</span>
                            </div>
                        </td>
                        <td>
                            <div>
                                <span>展示价格</span><span
                                        class="ml-5 label label-secondary">{{$data->show_price}}</span><span
                                        class="ml-5">元</span>
                            </div>
                            <div class="mt-5">
                                <span>实际售价</span><span class="ml-5 label label-primary">{{$data->price}}</span><span
                                        class="ml-5">元</span>
                            </div>
                            <div class="mt-5">
                                <span>总库存数</span><span class="ml-5 label label-primary">{{$data->total_num}}</span><span
                                        class="ml-5">个</span>
                            </div>
                            <div class="mt-5">
                                <span>已销售数</span><span class="ml-5 label label-success">{{$data->sale_num}}</span><span
                                        class="ml-5">个</span>
                            </div>
                            <div class="mt-5">
                                <span>剩余库存</span><span class="ml-5 label label-warning">{{$data->left_num}}</span><span
                                        class="ml-5">个</span>
                            </div>
                        </td>
                        <td>
                            <div>
                                <span>展示数</span><span class="ml-5 label label-secondary">{{$data->show_num}}</span>
                            </div>
                            <div class="mt-5">
                                <span>评论数</span><span class="ml-5 label label-primary">{{$data->comm_num}}</span>
                            </div>
                            <div class="mt-5">
                                <span>点赞数</span><span class="ml-5 label label-success">{{$data->zan_num}}</span>
                            </div>
                            <div class="mt-5">
                                <span>收藏数</span><span class="ml-5 label label-warning">{{$data->coll_num}}</span>
                            </div>
                            <div class="mt-5">
                                <span>转发数</span><span class="ml-5 label label-danger">{{$data->trans_num}}</span>
                            </div>
                        </td>
                        <td class="td-status">
                            <div>
                                <span class="label label-secondary">
                                    {{$data->status_str}}
                                </span>
                            </div>
                            <div class="mt-5">
                                <span class="label label-danger">{{$data->recomm_flag_str}}</span>
                            </div>
                            <div class="mt-5">
                                <span class="ml-5 label label-warning">{{$data->busi_name_str}}</span>
                            </div>
                        </td>
                        <td class="td-manage">
                            <div>
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
                            </div>
                            <div class="mt-5">
                                <a style="text-decoration:none" onClick="set_recomm(this,'{{$data->id}}')"
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
                                <a style="text-decoration:none"
                                   onClick="edit('编辑商品-{{$data->name}}','{{URL::asset('/admin/goods/edit')}}?id={{$data->id}}',{{$data->id}})"
                                   href="javascript:;" class="ml-5 c-primary"
                                   title="编辑商品">
                                    编辑商品
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
    </div>
@endsection

@section('script')
    <script type="text/javascript">


        $(function () {

        });


        /*商品-编辑*/
        function edit(title, url, id) {
            console.log("goods edit");
            creatIframe(url, title)
        }

        /*商品-隐藏*/
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
                goods_setStatus('{{URL::asset('')}}', param, function (ret) {
                    if (ret.result == true) {
                        $("#search_form").submit();
                    }
                })
//                <i class="Hui-iconfont">&#xe631;</i>
                layer.msg('已隐藏', {icon: 5, time: 1000});
            });
        }

        /*商品-显示*/
        function start(obj, id) {
            layer.confirm('确认要显示吗？', function (index) {
                //此处请求后台程序，下方是成功后的前台处理
                var param = {
                    id: id,
                    status: 1,
                    _token: "{{ csrf_token() }}"
                }
                //从后台设置商品状态
                goods_setStatus('{{URL::asset('')}}', param, function (ret) {
                    if (ret.result == true) {
                        $("#search_form").submit();
                    }
                })
                layer.msg('已显示', {icon: 6, time: 1000});
            });
        }


        /*
         * 设置推荐
         *
         * By TerryQi
         *
         * 2018-09-22
         */
        function set_recomm(obj, id) {
            layer.confirm('确认设置推荐标志吗？', function (index) {
                //此处请求后台程序，下方是成功后的前台处理
                var param = {
                    id: id,
                    recomm_flag: 1,
                    _token: "{{ csrf_token() }}"
                }
                //从后台设置商品状态
                goods_setStatus('{{URL::asset('')}}', param, function (ret) {
                    if (ret.result == true) {
                        $("#search_form").submit();
                    }
                })
                layer.msg('已设置推荐', {icon: 6, time: 1000});
            });
        }

        /*
         * 设置不推荐
         *
         * By TerryQi
         *
         * 2018-09-22
         */
        function not_recomm(obj, id) {
            layer.confirm('确认取消推荐标识吗？', function (index) {
                //此处请求后台程序，下方是成功后的前台处理
                var param = {
                    id: id,
                    recomm_flag: 0,
                    _token: "{{ csrf_token() }}"
                }
                //从后台设置商品状态
                goods_setStatus('{{URL::asset('')}}', param, function (ret) {
                    if (ret.result == true) {
                        $("#search_form").submit();
                    }
                })
                layer.msg('已取消推荐标识', {icon: 6, time: 1000});
            });
        }


    </script>
@endsection