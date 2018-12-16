@extends('admin.layouts.app')

@section('content')

    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 消息通知管理 <span
                class="c-gray en">&gt;</span> 消息通知详情 </nav>
    <div class="page-container">

        <h3 id="title">{{$data->title}}</h3>

        <div style="padding-top: 10px;">
            <span>发布人： {{$admin->name}}</span><span style="margin-left: 20px;">发布时间： {{$data->created_at}}</span>
        </div>


        <div style="margin-top: 20px;">
            {!! $data->content_html !!}
        </div>

    </div>
@endsection

@section('script')
    <script type="text/javascript">


        $(function () {

        });


    </script>
@endsection