@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 用户列表管理 <span
                class="c-gray en">&gt;</span> 用户列表 <a class="btn btn-success radius r btn-refresh"
                                                      style="line-height:1.6em;margin-top:3px"
                                                      title="刷新"
                                                      onclick="location.replace('{{URL::asset('/admin/ysb/ysbUser/index')}}');"><i
                    class="Hui-iconfont">&#xe68f;</i></a></nav>
    <div class="page-container">

        <div class="text-c">
            <form id="search_form" action="{{URL::asset('/admin/ysb/ysbUser/index')}}" method="post"
                  class="form-horizontal">
                {{csrf_field()}}
                <div class="Huiform text-r">
                    <span>关联用户id：</span>
                    <input id="user_id" name="user_id" type="text" class="input-text" style="width:150px"
                           placeholder="关联用户id" value="{{$con_arr['user_id']}}">

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
                    <th scope="col" colspan="8">用户列表</th>
                </tr>
                <tr class="text-c">
                    {{--<th width="25"><input type="checkbox" name="" value=""></th>--}}
                    <th width="40">ID</th>
                    <th width="50">头像</th>
                    <th width="50">昵称</th>
                    <th width="50">级别</th>
                    <th width="50">影响力</th>
                    <th width="80">创建时间</th>
                    <th width="80">状态</th>
                    <th width="80">操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($datas as $data)
                    <tr class="text-c">
                        {{--<td><input type="checkbox" value="1" name=""></td>--}}
                        <td>{{$data->id}}</td>
                        <td>
                            @if($data->user)
                                <img src="{{ $data->user->avatar ? $data->user->avatar.'?imageView2/1/w/200/h/200/interlace/1/q/75|imageslim' : URL::asset('/img/default_headicon.png')}}"
                                     class="img-rect-30 radius-5">
                            @endif
                        </td>
                        <td>
                            @if($data->user)
                                {{$data->user->nick_name}}({{$data->user->id}})
                            @endif
                        </td>
                        <td>
                            {{isset($data->level)?$data->level:'--'}}
                        </td>
                        <td>
                            {{isset($data->inf_value)?$data->inf_value:'--'}}
                        </td>
                        <td>{{$data->created_at}}</td>
                        <td class="td-status">
                            @if($data->status=="1")
                                <span class="label label-success radius">正常</span>
                            @else
                                <span class="label label-default radius">冻结</span>
                            @endif
                        </td>
                        <td class="td-manage">
                            <p>
                                @if($data->user)
                                    <a style="text-decoration:none"
                                       onClick="showArticles('{{$data->user->id}}','{{$data->user->nick_name}}')"
                                       href="javascript:;" class="c-primary"
                                       title="作品明细">
                                        作品明细
                                    </a>
                                @endif
                            </p>
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
         * 展示作品明细
         * 
         * By TerryQi
         *
         * 2018-09-21
         */
        function showArticles(user_id, nick_name) {

        }

    </script>
@endsection