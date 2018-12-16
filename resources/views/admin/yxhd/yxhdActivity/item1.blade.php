<div class="cl pd-5 bg-1 bk-gray mt-20">
    <span class="l">
        <a href="javascript:;"
           onclick="edit('添加活动奖品','{{URL::asset('/admin/yxhd/yxhdPrizeSetting/edit')}}?activity_id={{$data->id}}')"
           class="btn btn-primary radius">
            <i class="Hui-iconfont">&#xe600;</i> 添加活动奖品
        </a>
    </span>
</div>

<div class="mt-20">
    <table class="table table-border table-bordered table-bg table-sort">
        <thead>
        <tr>
            <th scope="col" colspan="7">奖品列表</th>
        </tr>
        <tr class="text-c">
            {{--<th width="25"><input type="checkbox" name="" value=""></th>--}}
            <th width="40">ID</th>
            <th width="200">奖品名称</th>
            <th width="50">库存</th>
            <th width="50">已发</th>
            <th width="50">中奖概率</th>
            <th width="50">操作</th>
        </tr>
        </thead>
        <tbody>
        @foreach($yxhdPrizeSettings as $yxhdPrizeSetting)
            <tr class="text-c">
                <td>
                    <span>{{$yxhdPrizeSetting->id}}</span>
                </td>
                <td>
                    <span>{{$yxhdPrizeSetting->prize->name}}</span>
                </td>
                <td>
                    <span class="c-primary">{{$yxhdPrizeSetting->prize->total_num}}</span>
                </td>
                <td>
                    <span class="c-primary">{{$yxhdPrizeSetting->prize->send_num}}</span>
                </td>
                <td class="td-status">
                    <span>{{$yxhdPrizeSetting->rate}}%</span>
                </td>
                <td class="td-manage">
                    <a title="编辑" href="javascript:;"
                       onclick="edit('编辑活动奖品','{{URL::asset('/admin/yxhd/yxhdPrizeSetting/edit')}}?activity_id={{$data->id}}&id={{$yxhdPrizeSetting->id}}')"
                       class="c-primary"
                       style="text-decoration:none">
                        编辑
                    </a>
                    <a title="删除" href="javascript:;"
                       onclick="del(this,'{{$yxhdPrizeSetting->id}}')"
                       class="ml-10 c-primary"
                       style="text-decoration:none">
                        删除
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>


<script type="text/javascript">

    $(function () {

    });

    /*
     * 配置活动信息
     *
     * By TerryQi
     *
     * 2018-12-11
     */
    function edit(title, url) {

        var index = layer.open({
            type: 2,
            area: ['650px', '380px'],
            fixed: false,
            maxmin: true,
            title: title,
            content: url
        });
    }


    /*删除奖品配置信息*/
    function del(obj, id) {
        layer.confirm('确认要删除吗？', function (index) {
            //进行后台删除
            var param = {
                id: id,
                _token: "{{ csrf_token() }}"
            }
            ajaxRequest('{{URL::asset('')}}' + "admin/yxhd/yxhdPrizeSetting/del/" + param.id, param, "GET", function (ret) {
                if (ret.result == true) {
                    layer.msg('已删除', {icon: 1, time: 1000});
                    $(".btn-refresh").click();
                } else {
                    layer.msg(ret.message, {icon: 5, time: 2000});
                }
                layer.close(index);
            });
        });
    }

</script>
