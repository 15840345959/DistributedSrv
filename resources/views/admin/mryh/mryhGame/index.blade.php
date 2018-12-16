@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 活动管理 <span
                class="c-gray en">&gt;</span> 活动列表 <a class="btn btn-success radius r btn-refresh"
                                                      style="line-height:1.6em;margin-top:3px"
                                                      title="刷新"
                                                      onclick="location.replace('{{URL::asset('/admin/mryh/mryhGame/index')}}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>
    <div class="page-container">

        <div class="text-c">
            <form id="search_form" action="{{URL::asset('/admin/mryh/mryhGame/index')}}" method="post"
                  class="form-horizontal">
                {{csrf_field()}}
                <div class="Huiform text-r">
                    <input id="search_word" name="search_word" type="text" class="input-text" style="width:250px"
                           placeholder="根据名称或关键词称进行检索" value="{{$con_arr['search_word']}}">
                    <input id="id" name="id" type="text" class="input-text" style="width:150px"
                           placeholder="活动id" value="{{$con_arr['id']}}">
                    <input id="creator_id" name="creator_id" type="text" class="input-text" style="width:150px"
                           placeholder="活动创建者id" value="{{$con_arr['creator_id']}}">
                    <span class="select-box" style="width: 80px;">
                        <select id="creator_type" name="creator_type" class="select">
                            <option value="">请选择</option>
                            @foreach(\App\Components\Utils::MRYH_GAME_CREATOR_TYPE_VAL as $key=>$value)
                                <option value="{{$key}}" {{$con_arr['creator_type']==strval($key)?'selected':''}}>{{$value}}</option>
                            @endforeach
                        </select>
                    </span>
                    <span class="ml-5">活动类型：</span>
                    <span class="select-box" style="width: 80px;">
                        <select id="type" name="type" class="select">
                            <option value="">请选择</option>
                            @foreach(\App\Components\Utils::MRYH_GAME_TYPE_VAL as $key=>$value)
                                <option value="{{$key}}" {{$con_arr['type']==strval($key)?'selected':''}}>{{$value}}</option>
                            @endforeach
                        </select>
                    </span>
                    <span class="ml-5">活动状态：</span>
                    <span class="select-box" style="width: 100px;">
                        <select id="game_status" name="game_status" class="select">
                            <option value="">请选择</option>
                            @foreach(\App\Components\Utils::MRYH_GAME_STATUS_VAL as $key=>$value)
                                <option value="{{$key}}" {{$con_arr['game_status']==strval($key)?'selected':''}}>{{$value}}</option>
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
                 <a href="javascript:;"
                    onclick="edit('新建活动','{{URL::asset('/admin/mryh/mryhGame/edit')}}')"
                    class="btn btn-primary radius">
                     <i class="Hui-iconfont">&#xe600;</i> 添加活动
                 </a>
            </span>
        </div>

        <div class="mt-20">
            <table class="table table-border table-bordered table-bg table-sort">
                <thead>
                <tr>
                    <th scope="col" colspan="7">活动列表
                        <span class="r">共有数据：<strong>{{$datas->total()}}</strong> 条</span></th>
                </tr>
                <tr class="text-c">
                    {{--<th width="25"><input type="checkbox" name="" value=""></th>--}}
                    <th width="150">活动名称</th>
                    <th width="200">基础信息</th>
                    <th width="100">数据</th>
                    <th width="50">状态</th>
                    <th width="80">操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($datas as $data)
                    <tr class="text-c">
                        <td>
                            <div>
                                <img src="{{$data->img}}" style="width: 80px;">
                            </div>
                            <div class="mt-5">
                                <span>{{$data->name}}</span>
                            </div>
                            <div>
                                <span class="c-primary">活动编号：{{$data->id}}</span>
                            </div>
                            <div>
                                <span>创建时间：{{$data->created_at}}</span>
                            </div>
                            <div class="mt-5">
                                <span>{{$data->creator_type_str}}
                                    /{{$data->creator_type==0?$data->creator->name:$data->creator->nick_name}}
                                    ({{$data->creator_id}})</span>
                            </div>
                            <div class="mt-5">
                                <span class="">参与上限：</span><span
                                        class="ml-5 c-primary">{{$data->max_join_num==0?'无上限':$data->max_join_num}}</span>
                            </div>
                            <div class="mt-5">
                                <span class="">目标天数：</span><span
                                        class="ml-5 c-primary">{{$data->target_join_day}}</span>
                                <span class="ml-15">参赛金额：</span><span
                                        class="ml-5 c-primary">{{$data->join_price}}</span>
                            </div>
                        </td>
                        <td>
                            <div>
                                <span>展示时间：</span><span class="ml-5">{{explode(' ',$data->show_start_time)[0]}}
                                    -{{explode(' ',$data->show_end_time)[0]}}</span>
                            </div>
                            <div class="mt-5">
                                <span>参赛时间：</span><span
                                        class="ml-5">{{explode(' ',$data->join_start_time)[0]}}
                                    -{{explode(' ',$data->join_end_time)[0]}}</span>
                            </div>
                            <div class="mt-5">
                                <span>开始时间：</span><span class="ml-5">{{explode(' ',$data->game_start_time)[0]}}
                                    -{{explode(' ',$data->game_end_time)[0]}}</span>
                            </div>
                            <div class="mt-5">
                                <span>展示状态：</span><span
                                        class="ml-5 c-primary label label-primary">{{$data->show_status_str}}</span>
                            </div>
                            <div class="mt-5">
                                <span class="">参加状态：</span><span
                                        class="ml-5 label label-secondary">{{$data->join_status_str}}</span>
                            </div>
                            <div class="mt-5">
                                <span class="">活动状态：</span><span
                                        class="ml-5 label label-success">{{$data->game_status_str}}</span>
                            </div>
                            <div class="mt-5">
                                <span class="">清分状态：</span><span
                                        class="ml-5 label label-danger">{{$data->jiesuan_status_str}}</span>
                            </div>
                        </td>
                        <td>
                            <div>
                                <span>参与人数</span><span class="ml-5 label label-secondary">{{$data->join_num}}</span>
                            </div>
                            <div class="mt-5">
                                <span>作品数量</span><span class="ml-5 label label-primary">{{$data->work_num}}</span>
                            </div>
                            <div class="mt-5">
                                <span>展示总量</span><span class="ml-5 label label-success">{{$data->show_num}}</span>
                            </div>
                            <div class="mt-5">
                                <span>预置奖金</span><span class="ml-5 label label-secondary">{{$data->adv_price}}</span>
                            </div>
                            <div class="mt-5">
                                <span>奖金总额</span><span class="ml-5 label label-warning">{{$data->total_money}}</span>
                            </div>
                            <div class="mt-5">
                                <span>成功人数</span><span class="ml-5 label label-danger">{{$data->success_num}}</span>
                            </div>
                            <div class="mt-5">
                                <span>失败人数</span><span class="ml-5 label label-primary">{{$data->fail_num}}</span>
                            </div>
                            <div class="mt-5">
                                <span>投诉人数</span><span class="ml-5 label label-secondary">{{$data->complain_num}}</span>
                            </div>
                        </td>
                        <td class="td-status">
                            <div>
                                @if($data->status=="1")
                                    <span class="label label-success radius">正常</span>
                                @else
                                    <span class="label label-default radius">冻结</span>
                                @endif
                            </div>
                            <div class="mt-5">
                                <span class="label label-success radius">{{$data->type_str}}</span>
                            </div>
                        </td>
                        <td class="td-manage">

                            <div>
                                @if($data->status=="1")
                                    <a style="text-decoration:none" onClick="stop(this,'{{$data->id}}')"
                                       href="javascript:;" class="c-primary"
                                       title="停用">
                                        停用
                                    </a>
                                @else
                                    <a style="text-decoration:none" onClick="start(this,'{{$data->id}}')"
                                       href="javascript:;" class="c-primary"
                                       title="启用">
                                        启用
                                    </a>
                                @endif
                                <a title="编辑" href="javascript:;"
                                   onclick="edit('编辑活动-{{$data->name}}','{{URL::asset('/admin/mryh/mryhGame/edit')}}?id={{$data->id}})',{{$data->id}})"
                                   class="c-primary ml-5" style="text-decoration:none">
                                    编辑
                                </a>
                            </div>
                            <div class="mt-10">
                                <a style="text-decoration:none" onClick="copy('{{$data->id}}')"
                                   href="javascript:;" class="c-primary"
                                   title="复制活动">
                                    复制活动
                                </a>
                            </div>
                            <div class="mt-10">
                                <a style="text-decoration:none" onClick="joinDetail('{{$data->id}}','{{$data->name}}')"
                                   href="javascript:;" class="c-primary"
                                   title="参赛明细">
                                    参赛明细
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

        /*
         参数解释：
         title	标题
         url		请求的url
         id		需要操作的数据id
         w		弹出层宽度（缺省调默认值）
         h		弹出层高度（缺省调默认值）
         */
        /*活动-增加*/
        function edit(title, url) {
            creatIframe(url, title)
        }

        /*活动-删除*/
        function del(obj, id) {
            layer.alert('不能删除活动，否则将导致数据混乱，请联系技术团队 TerryQi负责');
        }

        /*活动-停用*/
        function stop(obj, id) {
            consoledebug.log("stop id:" + id);
            layer.confirm('确认要停用吗？', function (index) {
                //此处请求后台程序，下方是成功后的前台处理
                var param = {
                    id: id,
                    status: 0,
                    _token: "{{ csrf_token() }}"
                }
                var index = layer.load(2, {time: 10 * 1000}); //加载
                //从后台设置活动状态
                mryh_mryhGame_setStatus('{{URL::asset('')}}', param, function (ret) {
                    if (ret.result == true) {
                        layer.msg('已停用', {icon: 5, time: 1000});
                        $("#search_form").submit();
                    } else {
                        layer.msg(ret.message, {icon: 5, time: 2000});
                    }
                    layer.close(index);
                })

            });
        }

        /*活动-启用*/
        function start(obj, id) {
            layer.confirm('确认要启用吗？', function (index) {
                //此处请求后台程序，下方是成功后的前台处理
                var param = {
                    id: id,
                    status: 1,
                    _token: "{{ csrf_token() }}"
                }

                var index = layer.load(2, {time: 10 * 1000}); //加载
                //从后台设置活动状态
                mryh_mryhGame_setStatus('{{URL::asset('')}}', param, function (ret) {
                    if (ret.result == true) {
                        layer.msg('正常', {icon: 6, time: 1000});
                        $("#search_form").submit();
                    } else {
                        layer.msg(ret.message, {icon: 5, time: 2000});
                    }
                    layer.close(index);
                })

            });
        }

        /*
         * 复制活动
         *
         * By TerryQi
         *
         * 2018-07-24
         *
         */
        function copy(activity_id) {
            layer.confirm('确认要复制该活动吗？', function (index) {
                //此处请求后台程序，下方是成功后的前台处理
                var param = {
                    id: activity_id,
                    _token: "{{ csrf_token() }}"
                }
                var index = layer.load(2, {time: 10 * 1000}); //加载
                //从后台设置活动状态
                mryh_mryhGame_copy('{{URL::asset('')}}', param, function (ret) {
                    if (ret.result == true) {
                        layer.msg('复制成功', {icon: 1, time: 1000});
                        //打开新的编辑窗口
                        creatIframe("{{URL::asset('/admin/mryh/mryhGame/edit')}}?id=" + ret.ret.id + "", "编辑活动-" + ret.ret.name);
                        $("#search_form").submit();
                    } else {
                        layer.msg(ret.message, {icon: 5, time: 2000});
                    }
                    layer.close(index);
                })
            });

        }


        /*
         * 参赛明细
         * 
         * By TerryQi
         * 
         */
        function joinDetail(game_id, game_name) {
            creatIframe("{{URL::asset('/admin/mryh/mryhJoin/index')}}?game_id=" + game_id, "参赛明细-" + game_name);
        }

    </script>
@endsection