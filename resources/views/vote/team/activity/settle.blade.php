@extends('admin.layouts.app')

@section('content')



    <div class="panel panel-primary mt-20" style="margin: 20px;">
        <div class="panel-header">操作记录

        </div>
        <div class="panel-body">

            <table class="table table-border table-bordered table-bg table-hover table-sort" id="table-sort">
                <thead>
                <tr>
                    <th scope="col" colspan="10">记录列表</th>
                </tr>
                <tr class="text-c">
                    <th width="40">ID</th>
                    <th width="50">操作员</th>
                    <th width="80">操作时间</th>
                    <th width="80">操作值</th>
                    <th width="100">附件</th>
                    <th width="300">备注</th>
                </tr>
                </thead>
                <tbody>
                @foreach($optRecords as $optRecord)
                    <tr class="text-c">
                        <td>{{$optRecord->id}}</td>
                        <td>{{isset($optRecord->admin)?$optRecord->admin->name:'--'}}</td>
                        <td>{{$optRecord->created_at}}</td>
                        <td>{{isset($optRecord->optInfo)?$optRecord->optInfo->name:'--'}}</td>
                        <td>
                            @if($optRecord->attach)
                                <a href="{{$optRecord->attach}}" target="_blank" style="color: #0e90d2;">点击查看</a>
                            @else
                                --
                            @endif
                        </td>
                        <td>{{$optRecord->remark}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="mt-20">
                <span class="btn btn-primary size-M radius"
                      onclick="layer_show('记录处理过程', '{{route('optRecord.edit', ['f_table' => 'activity', 'f_id' => $data->id])}}',800,380)">记录处理过程</span>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script type="text/javascript">

        function refresh() {
            location.replace('{{ route('voteActivity.settle', ['id' => $data->id]) }}')
        }

    </script>
@endsection